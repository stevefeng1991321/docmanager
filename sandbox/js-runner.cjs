'use strict';

// Offline JS auto-grader harness. Invoked as: node js-runner.js <payload.json path>
// Payload: { code: string, functionName: string, cases: [{ args: any[], expected: any }] }
//
// Always prints exactly one JSON object to stdout and exits 0, even on failure,
// so the calling PHP process never has to parse a non-JSON crash dump.

const fs = require('fs');
const vm = require('vm');

function deepEqual(a, b) {
    if (a === b) return true;
    if (typeof a !== typeof b) return false;
    if (a === null || b === null) return a === b;
    if (typeof a === 'number') {
        // Tolerate floating-point rounding differences from equivalent
        // but differently-ordered arithmetic (e.g. activation functions).
        return Math.abs(a - b) <= 1e-9 * Math.max(1, Math.abs(a), Math.abs(b));
    }
    if (typeof a !== 'object') return false;

    if (Array.isArray(a) || Array.isArray(b)) {
        if (!Array.isArray(a) || !Array.isArray(b)) return false;
        if (a.length !== b.length) return false;
        for (let i = 0; i < a.length; i++) {
            if (!deepEqual(a[i], b[i])) return false;
        }
        return true;
    }

    const aKeys = Object.keys(a);
    const bKeys = Object.keys(b);
    if (aKeys.length !== bKeys.length) return false;
    return aKeys.every((key) => deepEqual(a[key], b[key]));
}

function buildSandbox() {
    const blockedRequire = () => {
        throw new Error('require() is not available in the sandbox');
    };

    return {
        require: blockedRequire,
        console: { log() {}, error() {}, warn() {}, info() {} },
        Object, Array, Math, JSON, String, Number, Boolean,
        Map, Set, RegExp, Date, Error, TypeError, RangeError,
        Infinity, NaN, undefined,
    };
}

function emit(result) {
    process.stdout.write(JSON.stringify(result));
}

function main() {
    const payloadPath = process.argv[2];
    if (!payloadPath) {
        emit({ passed: 0, total: 0, results: [], fatal: 'No payload path provided.' });
        return;
    }

    let payload;
    try {
        payload = JSON.parse(fs.readFileSync(payloadPath, 'utf8'));
    } catch (e) {
        emit({ passed: 0, total: 0, results: [], fatal: `Could not read payload: ${e.message}` });
        return;
    }

    const { code, functionName, cases } = payload;
    const total = Array.isArray(cases) ? cases.length : 0;

    const sandbox = buildSandbox();
    const context = vm.createContext(sandbox);

    try {
        vm.runInContext(code, context, { timeout: 5000 });
    } catch (e) {
        emit({ passed: 0, total, results: [], fatal: `Error loading code: ${e.message}` });
        return;
    }

    const fn = sandbox[functionName];
    if (typeof fn !== 'function') {
        emit({ passed: 0, total, results: [], fatal: `Function "${functionName}" was not found.` });
        return;
    }

    let passed = 0;
    const results = (cases || []).map((testCase) => {
        const { args, expected } = testCase;
        try {
            const actual = vm.runInContext(
                `(${functionName})(...${JSON.stringify(args)})`,
                context,
                { timeout: 5000 }
            );
            const pass = deepEqual(actual, expected);
            if (pass) passed++;
            return { pass, expected, actual };
        } catch (e) {
            return { pass: false, expected, actual: null, error: e.message };
        }
    });

    emit({ passed, total, results });
}

main();
