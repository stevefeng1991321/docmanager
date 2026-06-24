<?php

namespace Database\Seeders;

use App\Models\Problem;
use Illuminate\Database\Seeder;

class MathSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->problems() as $i => $p) {
            Problem::updateOrCreate(
                ['order_index' => 200 + $i + 1],
                array_merge($p, ['order_index' => 200 + $i + 1, 'category' => 'Math'])
            );
        }

        $this->command->info('Seeded ' . count($this->problems()) . ' Math problems (201–230).');
    }

    private function problems(): array
    {
        return [

            // ─── EASY ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Fibonacci Sequence',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the nth Fibonacci number (0-indexed). F(0)=0, F(1)=1. Use an iterative approach for O(n) time and O(1) space.',
                'solution_code' => <<<'JS'
function fibonacci(n) {
    if (n <= 1) return n;
    let a = 0, b = 1;
    for (let i = 2; i <= n; i++) [a, b] = [b, a + b];
    return b;
}

console.log(fibonacci(0));    // 0
console.log(fibonacci(1));    // 1
console.log(fibonacci(7));    // 13
console.log(fibonacci(10));   // 55
console.log(fibonacci(20));   // 6765
JS,
            ],
            [
                'title'       => 'Factorial',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the factorial of a non-negative integer n. By convention, 0! = 1.',
                'solution_code' => <<<'JS'
function factorial(n) {
    if (n < 0) throw new Error("Negative input");
    let result = 1;
    for (let i = 2; i <= n; i++) result *= i;
    return result;
}

console.log(factorial(0));    // 1
console.log(factorial(5));    // 120
console.log(factorial(10));   // 3628800
JS,
            ],
            [
                'title'       => 'Prime Number Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a number is prime. Use trial division up to √n for efficiency.',
                'solution_code' => <<<'JS'
function isPrime(n) {
    if (n < 2) return false;
    for (let i = 2; i <= Math.sqrt(n); i++) {
        if (n % i === 0) return false;
    }
    return true;
}

console.log(isPrime(2));    // true
console.log(isPrime(17));   // true
console.log(isPrime(1));    // false
console.log(isPrime(25));   // false
console.log(isPrime(97));   // true
JS,
            ],
            [
                'title'       => 'GCD (Greatest Common Divisor)',
                'difficulty'  => 'easy',
                'description' => 'Find the GCD of two positive integers using the Euclidean algorithm: GCD(a, b) = GCD(b, a % b).',
                'solution_code' => <<<'JS'
function gcd(a, b) {
    while (b !== 0) [a, b] = [b, a % b];
    return a;
}

console.log(gcd(48, 18));    // 6
console.log(gcd(100, 75));   // 25
console.log(gcd(7, 5));      // 1
console.log(gcd(0, 5));      // 5
JS,
            ],
            [
                'title'       => 'LCM (Least Common Multiple)',
                'difficulty'  => 'easy',
                'description' => 'Find the LCM of two positive integers. Use: LCM(a, b) = (a × b) / GCD(a, b). Also compute the LCM of an array.',
                'solution_code' => <<<'JS'
function gcd(a, b) {
    while (b !== 0) [a, b] = [b, a % b];
    return a;
}

function lcm(a, b) {
    return (a / gcd(a, b)) * b;
}

function lcmArray(nums) {
    return nums.reduce(lcm);
}

console.log(lcm(4, 6));               // 12
console.log(lcm(12, 18));             // 36
console.log(lcmArray([2, 3, 4, 5])); // 60
JS,
            ],
            [
                'title'       => 'Power of Two Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a number is a power of two using the bit trick: n & (n−1) === 0.',
                'solution_code' => <<<'JS'
function isPowerOfTwo(n) {
    if (n <= 0) return false;
    return (n & (n - 1)) === 0;
}

console.log(isPowerOfTwo(1));    // true
console.log(isPowerOfTwo(16));   // true
console.log(isPowerOfTwo(18));   // false
console.log(isPowerOfTwo(0));    // false
JS,
            ],
            [
                'title'       => 'Sum of Digits',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the sum of digits of a positive integer.',
                'solution_code' => <<<'JS'
function sumOfDigits(n) {
    return String(Math.abs(n))
        .split("")
        .reduce((sum, d) => sum + Number(d), 0);
}

console.log(sumOfDigits(123));    // 6
console.log(sumOfDigits(9999));   // 36
console.log(sumOfDigits(0));      // 0
console.log(sumOfDigits(-42));    // 6
JS,
            ],
            [
                'title'       => 'Celsius to Fahrenheit',
                'difficulty'  => 'easy',
                'description' => 'Write functions that convert temperatures between Celsius and Fahrenheit.',
                'solution_code' => <<<'JS'
function celsiusToFahrenheit(c) {
    return (c * 9 / 5) + 32;
}

function fahrenheitToCelsius(f) {
    return (f - 32) * 5 / 9;
}

console.log(celsiusToFahrenheit(0));      // 32
console.log(celsiusToFahrenheit(100));    // 212
console.log(celsiusToFahrenheit(-40));    // -40
console.log(fahrenheitToCelsius(32));     // 0
JS,
            ],
            [
                'title'       => 'Sieve of Eratosthenes',
                'difficulty'  => 'easy',
                'description' => 'Return all prime numbers up to and including n using the Sieve of Eratosthenes.',
                'solution_code' => <<<'JS'
function sieve(n) {
    const isPrime = new Array(n + 1).fill(true);
    isPrime[0] = isPrime[1] = false;
    for (let i = 2; i * i <= n; i++) {
        if (isPrime[i]) {
            for (let j = i * i; j <= n; j += i) isPrime[j] = false;
        }
    }
    return isPrime.reduce((primes, flag, num) => {
        if (flag) primes.push(num);
        return primes;
    }, []);
}

console.log(sieve(20));           // [2, 3, 5, 7, 11, 13, 17, 19]
console.log(sieve(50).length);    // 15 primes
JS,
            ],
            [
                'title'       => "Pascal's Triangle Row",
                'difficulty'  => 'easy',
                'description' => "Return the nth row of Pascal's Triangle (0-indexed). Each value is the binomial coefficient C(n, k).",
                'solution_code' => <<<'JS'
function pascalRow(n) {
    const row = [1];
    for (let k = 1; k <= n; k++) {
        row.push(row[k - 1] * (n - k + 1) / k);
    }
    return row;
}

console.log(pascalRow(0));    // [1]
console.log(pascalRow(1));    // [1, 1]
console.log(pascalRow(4));    // [1, 4, 6, 4, 1]
console.log(pascalRow(5));    // [1, 5, 10, 10, 5, 1]
JS,
            ],

            // ─── MEDIUM ──────────────────────────────────────────────────────────────

            [
                'title'       => 'Fast Exponentiation',
                'difficulty'  => 'medium',
                'description' => 'Compute base^exp in O(log n) time using exponentiation by squaring. Also support negative exponents.',
                'solution_code' => <<<'JS'
function fastPow(base, exp) {
    if (exp < 0) return 1 / fastPow(base, -exp);
    if (exp === 0) return 1;
    if (exp % 2 === 0) {
        const half = fastPow(base, exp / 2);
        return half * half;
    }
    return base * fastPow(base, exp - 1);
}

console.log(fastPow(2, 10));    // 1024
console.log(fastPow(3, 5));     // 243
console.log(fastPow(2, -3));    // 0.125
console.log(fastPow(7, 0));     // 1
JS,
            ],
            [
                'title'       => 'Prime Factorization',
                'difficulty'  => 'medium',
                'description' => 'Return the prime factors of n as an array (with repetition). E.g. 12 → [2, 2, 3]. Divide by 2 first, then check odd numbers up to √n.',
                'solution_code' => <<<'JS'
function primeFactors(n) {
    const factors = [];
    while (n % 2 === 0) { factors.push(2); n /= 2; }
    for (let i = 3; i <= Math.sqrt(n); i += 2) {
        while (n % i === 0) { factors.push(i); n /= i; }
    }
    if (n > 2) factors.push(n);
    return factors;
}

console.log(primeFactors(12));    // [2, 2, 3]
console.log(primeFactors(28));    // [2, 2, 7]
console.log(primeFactors(100));   // [2, 2, 5, 5]
console.log(primeFactors(97));    // [97] (prime)
JS,
            ],
            [
                'title'       => 'Binomial Coefficient (nCr)',
                'difficulty'  => 'medium',
                'description' => 'Compute C(n, r) = n! / (r! × (n−r)!) using the multiplicative formula to avoid large factorials.',
                'solution_code' => <<<'JS'
function nCr(n, r) {
    if (r < 0 || r > n) return 0;
    if (r === 0 || r === n) return 1;
    r = Math.min(r, n - r);
    let result = 1;
    for (let i = 0; i < r; i++) {
        result = result * (n - i) / (i + 1);
    }
    return Math.round(result);
}

console.log(nCr(5, 2));     // 10
console.log(nCr(10, 3));    // 120
console.log(nCr(6, 6));     // 1
console.log(nCr(52, 5));    // 2598960
JS,
            ],
            [
                'title'       => 'Standard Deviation',
                'difficulty'  => 'medium',
                'description' => 'Compute both the population standard deviation σ = √((1/n)·Σ(xi−μ)²) and the sample standard deviation (dividing by n−1).',
                'solution_code' => <<<'JS'
function standardDeviation(nums, sample = false) {
    const n    = nums.length;
    const mean = nums.reduce((s, x) => s + x, 0) / n;
    const variance = nums.reduce((s, x) => s + (x - mean) ** 2, 0) / (sample ? n - 1 : n);
    return Math.sqrt(variance);
}

const data = [2, 4, 4, 4, 5, 5, 7, 9];
console.log(+standardDeviation(data).toFixed(4));         // 2 (population)
console.log(+standardDeviation(data, true).toFixed(4));   // 2.1381 (sample)
JS,
            ],
            [
                'title'       => 'Matrix Multiplication',
                'difficulty'  => 'medium',
                'description' => 'Write a function that multiplies two matrices. If A is m×k and B is k×n, the result is m×n.',
                'solution_code' => <<<'JS'
function matMul(A, B) {
    const m = A.length, k = A[0].length, n = B[0].length;
    return Array.from({ length: m }, (_, i) =>
        Array.from({ length: n }, (_, j) =>
            A[i].reduce((sum, _, r) => sum + A[i][r] * B[r][j], 0)
        )
    );
}

const A = [[1, 2], [3, 4]];
const B = [[5, 6], [7, 8]];
console.log(matMul(A, B));
// [[19, 22], [43, 50]]

const I = [[1, 0], [0, 1]];
console.log(JSON.stringify(matMul(A, I)));   // [[1,2],[3,4]] (identity)
JS,
            ],
            [
                'title'       => 'Mean, Median and Mode',
                'difficulty'  => 'medium',
                'description' => 'Compute the three measures of central tendency for an array of numbers: Mean, Median, and Mode (most frequent values).',
                'solution_code' => <<<'JS'
function statistics(nums) {
    const n    = nums.length;
    const mean = nums.reduce((s, x) => s + x, 0) / n;

    const sorted = [...nums].sort((a, b) => a - b);
    const median = n % 2 === 0
        ? (sorted[n / 2 - 1] + sorted[n / 2]) / 2
        : sorted[Math.floor(n / 2)];

    const freq = nums.reduce((map, x) => {
        map[x] = (map[x] || 0) + 1;
        return map;
    }, {});
    const maxFreq = Math.max(...Object.values(freq));
    const mode = Object.keys(freq)
        .filter(k => freq[k] === maxFreq)
        .map(Number);

    return { mean, median, mode };
}

const s = statistics([1, 2, 2, 3, 4, 4, 4, 5]);
console.log("Mean:",   s.mean);      // 3.125
console.log("Median:", s.median);    // 3.5
console.log("Mode:",   s.mode);      // [4]
JS,
            ],
            [
                'title'       => 'Count Trailing Zeros in Factorial',
                'difficulty'  => 'medium',
                'description' => 'Count the number of trailing zeros in n!. Each trailing zero comes from a factor of 10 = 2×5. Since factors of 2 always exceed 5s, count multiples of 5, 25, 125, … in n.',
                'solution_code' => <<<'JS'
function trailingZeros(n) {
    let count = 0;
    while (n >= 5) {
        n = Math.floor(n / 5);
        count += n;
    }
    return count;
}

console.log(trailingZeros(5));     // 1   (5! = 120)
console.log(trailingZeros(10));    // 2   (10! = 3628800)
console.log(trailingZeros(25));    // 6   (25 and 5² each contribute)
console.log(trailingZeros(100));   // 24
JS,
            ],
            [
                'title'       => 'Integer Square Root',
                'difficulty'  => 'medium',
                'description' => 'Find the integer square root of n (floor of √n) without using Math.sqrt. Use binary search for O(log n) time.',
                'solution_code' => <<<'JS'
function isqrt(n) {
    if (n < 0) throw new Error("Negative input");
    if (n === 0) return 0;
    let lo = 1, hi = n, result = 0;
    while (lo <= hi) {
        const mid = Math.floor((lo + hi) / 2);
        if (mid * mid <= n) {
            result = mid;
            lo = mid + 1;
        } else {
            hi = mid - 1;
        }
    }
    return result;
}

console.log(isqrt(0));    // 0
console.log(isqrt(9));    // 3
console.log(isqrt(10));   // 3  (floor of 3.162...)
console.log(isqrt(16));   // 4
console.log(isqrt(26));   // 5
JS,
            ],
            [
                'title'       => 'Aliquot Sum and Number Classification',
                'difficulty'  => 'medium',
                'description' => 'The aliquot sum of n is the sum of its proper divisors (all divisors except itself). Classify n as: perfect (aliquot = n), abundant (aliquot > n), or deficient (aliquot < n).',
                'solution_code' => <<<'JS'
function aliquotSum(n) {
    if (n <= 1) return 0;
    let sum = 1;
    for (let i = 2; i <= Math.sqrt(n); i++) {
        if (n % i === 0) {
            sum += i;
            if (i !== n / i) sum += n / i;
        }
    }
    return sum;
}

function classify(n) {
    const s = aliquotSum(n);
    if (s === n) return "perfect";
    if (s > n)  return "abundant";
    return "deficient";
}

console.log(aliquotSum(6));     // 6   → perfect
console.log(aliquotSum(12));    // 16  → abundant
console.log(aliquotSum(8));     // 7   → deficient

console.log(classify(6));    // "perfect"
console.log(classify(12));   // "abundant"
console.log(classify(8));    // "deficient"
JS,
            ],
            [
                'title'       => 'Geometric Sequence Sum',
                'difficulty'  => 'medium',
                'description' => 'Compute the sum of the first n terms of a geometric sequence with first term a and common ratio r, using the closed-form formula. Handle r = 1 as a special case.',
                'solution_code' => <<<'JS'
function geometricSum(a, r, n) {
    if (r === 1) return a * n;
    return a * (1 - Math.pow(r, n)) / (1 - r);
}

function geometricTerms(a, r, n) {
    return Array.from({ length: n }, (_, i) => a * Math.pow(r, i));
}

console.log(geometricSum(1, 2, 5));     // 31   (1+2+4+8+16)
console.log(geometricSum(3, 3, 4));     // 120  (3+9+27+81)
console.log(geometricSum(5, 1, 4));     // 20   (5+5+5+5)
console.log(geometricTerms(1, 2, 5));   // [1, 2, 4, 8, 16]
JS,
            ],

            // ─── HARD ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Modular Exponentiation',
                'difficulty'  => 'hard',
                'description' => 'Compute (base^exp) % mod efficiently in O(log exp) time using fast exponentiation with modular reduction at each step. Essential in cryptography (RSA, Diffie-Hellman).',
                'solution_code' => <<<'JS'
function modPow(base, exp, mod) {
    if (mod === 1) return 0;
    let result = 1;
    base = base % mod;
    while (exp > 0) {
        if (exp % 2 === 1) result = (result * base) % mod;
        exp = Math.floor(exp / 2);
        base = (base * base) % mod;
    }
    return result;
}

console.log(modPow(2, 10, 1000));    // 24  (1024 % 1000)
console.log(modPow(3, 200, 13));     // 9
console.log(modPow(2, 31, 1000000007));  // 2147483648 % 1e9+7 = 147483641

// Verify: 2^10 = 1024, 1024 % 1000 = 24
console.log(2 ** 10 % 1000);    // 24
JS,
            ],
            [
                'title'       => 'Extended Euclidean Algorithm',
                'difficulty'  => 'hard',
                'description' => 'Compute the Extended GCD: find integers x, y such that a·x + b·y = GCD(a, b). Use this to compute the modular inverse: a⁻¹ mod m (exists when GCD(a, m) = 1).',
                'solution_code' => <<<'JS'
function extGCD(a, b) {
    if (b === 0) return { gcd: a, x: 1, y: 0 };
    const { gcd, x: x1, y: y1 } = extGCD(b, a % b);
    return { gcd, x: y1, y: x1 - Math.floor(a / b) * y1 };
}

function modInverse(a, m) {
    const { gcd, x } = extGCD(a, m);
    if (gcd !== 1) return null;    // inverse doesn't exist
    return ((x % m) + m) % m;
}

const r = extGCD(35, 15);
console.log(r);   // { gcd: 5, x: 1, y: -2 }  → 35·1 + 15·(-2) = 5

console.log(modInverse(3, 7));    // 5  (3·5 ≡ 1 mod 7)
console.log(modInverse(7, 13));   // 2  (7·2 = 14 ≡ 1 mod 13)
console.log(modInverse(4, 8));    // null (GCD(4,8) = 4 ≠ 1)
JS,
            ],
            [
                'title'       => 'Matrix Exponentiation (Fast Fibonacci)',
                'difficulty'  => 'hard',
                'description' => 'Compute the nth Fibonacci number in O(log n) using matrix exponentiation. The identity: [[1,1],[1,0]]^n = [[F(n+1),F(n)],[F(n),F(n-1)]].',
                'solution_code' => <<<'JS'
function matMul(A, B) {
    return [
        [A[0][0]*B[0][0] + A[0][1]*B[1][0], A[0][0]*B[0][1] + A[0][1]*B[1][1]],
        [A[1][0]*B[0][0] + A[1][1]*B[1][0], A[1][0]*B[0][1] + A[1][1]*B[1][1]],
    ];
}

function matPow(M, n) {
    if (n === 1) return M;
    if (n % 2 === 0) {
        const half = matPow(M, n / 2);
        return matMul(half, half);
    }
    return matMul(M, matPow(M, n - 1));
}

function fibonacci(n) {
    if (n <= 1) return n;
    const base = [[1, 1], [1, 0]];
    return matPow(base, n)[0][1];
}

console.log(fibonacci(0));    // 0
console.log(fibonacci(1));    // 1
console.log(fibonacci(10));   // 55
console.log(fibonacci(20));   // 6765
console.log(fibonacci(45));   // 1134903170
JS,
            ],
            [
                'title'       => 'Miller-Rabin Primality Test',
                'difficulty'  => 'hard',
                'description' => 'Implement the Miller-Rabin probabilistic primality test. Write n−1 as 2^s·d, then test several witnesses. With witnesses {2,3,5,7,11,13,17,19,23} it is deterministic for n < 3,317,044,064,679,887,385,961,981.',
                'solution_code' => <<<'JS'
function modPow(base, exp, mod) {
    let result = 1n;
    base = base % mod;
    while (exp > 0n) {
        if (exp % 2n === 1n) result = result * base % mod;
        exp >>= 1n;
        base = base * base % mod;
    }
    return result;
}

function millerRabin(n) {
    if (n < 2n) return false;
    if (n === 2n || n === 3n) return true;
    if (n % 2n === 0n) return false;

    let s = 0n, d = n - 1n;
    while (d % 2n === 0n) { d /= 2n; s++; }

    const witnesses = [2n, 3n, 5n, 7n, 11n, 13n, 17n, 19n, 23n];
    for (const a of witnesses) {
        if (a >= n) continue;
        let x = modPow(a, d, n);
        if (x === 1n || x === n - 1n) continue;
        let composite = true;
        for (let r = 1n; r < s; r++) {
            x = x * x % n;
            if (x === n - 1n) { composite = false; break; }
        }
        if (composite) return false;
    }
    return true;
}

console.log(millerRabin(2n));          // true
console.log(millerRabin(17n));         // true
console.log(millerRabin(97n));         // true
console.log(millerRabin(100n));        // false
console.log(millerRabin(104729n));     // true  (10000th prime)
JS,
            ],
            [
                'title'       => 'Integer Partition Count',
                'difficulty'  => 'hard',
                'description' => 'Count the number of ways to write n as an ordered sum of positive integers (partitions). E.g. p(4) = 5: {4}, {3,1}, {2,2}, {2,1,1}, {1,1,1,1}. Use dynamic programming.',
                'solution_code' => <<<'JS'
function countPartitions(n) {
    const dp = new Array(n + 1).fill(0);
    dp[0] = 1;
    for (let i = 1; i <= n; i++) {
        for (let j = i; j <= n; j++) {
            dp[j] += dp[j - i];
        }
    }
    return dp[n];
}

console.log(countPartitions(1));    // 1
console.log(countPartitions(4));    // 5
console.log(countPartitions(5));    // 7
console.log(countPartitions(10));   // 42
console.log(countPartitions(20));   // 627
JS,
            ],
            [
                'title'       => 'Josephus Problem',
                'difficulty'  => 'hard',
                'description' => 'n people stand in a circle. Starting from position 0, every kth person is eliminated. Find the position (0-indexed) of the last survivor. Solve in O(n) using the recurrence: J(1,k)=0, J(n,k)=(J(n-1,k)+k) % n.',
                'solution_code' => <<<'JS'
function josephus(n, k) {
    let pos = 0;
    for (let i = 2; i <= n; i++) {
        pos = (pos + k) % i;
    }
    return pos;
}

console.log(josephus(7, 3));    // 3  (classic puzzle: 0-indexed)
console.log(josephus(5, 2));    // 2
console.log(josephus(1, 1));    // 0
console.log(josephus(10, 2));   // 2

// Show full elimination order for n=7, k=3
function josephusOrder(n, k) {
    const people = Array.from({ length: n }, (_, i) => i);
    const order = [];
    let idx = 0;
    while (people.length > 0) {
        idx = (idx + k - 1) % people.length;
        order.push(people.splice(idx, 1)[0]);
        if (idx >= people.length) idx = 0;
    }
    return order;
}
console.log(josephusOrder(7, 3));   // [2,5,1,6,4,0,3] → survivor: 3
JS,
            ],
            [
                'title'       => "Newton's Method (nth Root)",
                'difficulty'  => 'hard',
                'description' => "Use Newton's method to compute the nth root of a positive number x. The iteration is: guess = ((n-1)·guess + x/guess^(n-1)) / n, converging quadratically.",
                'solution_code' => <<<'JS'
function nthRoot(x, n, tolerance = 1e-10) {
    if (x < 0 && n % 2 === 0) throw new Error("Even root of negative number");
    let guess = x / n;
    while (true) {
        const next = ((n - 1) * guess + x / Math.pow(guess, n - 1)) / n;
        if (Math.abs(next - guess) < tolerance) return next;
        guess = next;
    }
}

console.log(+nthRoot(8, 3).toFixed(10));      // 2.0000000000  (∛8)
console.log(+nthRoot(16, 4).toFixed(10));     // 2.0000000000  (⁴√16)
console.log(+nthRoot(2, 2).toFixed(6));       // 1.414214      (√2)
console.log(+nthRoot(1000, 3).toFixed(6));    // 10.000000     (∛1000)
JS,
            ],
            [
                'title'       => 'Convex Hull (Graham Scan)',
                'difficulty'  => 'hard',
                'description' => "Find the convex hull of a set of 2D points using Graham's scan algorithm in O(n log n). Returns the hull vertices in counter-clockwise order.",
                'solution_code' => <<<'JS'
function cross(O, A, B) {
    return (A.x - O.x) * (B.y - O.y) - (A.y - O.y) * (B.x - O.x);
}

function convexHull(points) {
    const n = points.length;
    if (n < 3) return points;

    points = [...points].sort((a, b) => a.x !== b.x ? a.x - b.x : a.y - b.y);

    const lower = [];
    for (const p of points) {
        while (lower.length >= 2 && cross(lower[lower.length-2], lower[lower.length-1], p) <= 0)
            lower.pop();
        lower.push(p);
    }

    const upper = [];
    for (let i = n - 1; i >= 0; i--) {
        const p = points[i];
        while (upper.length >= 2 && cross(upper[upper.length-2], upper[upper.length-1], p) <= 0)
            upper.pop();
        upper.push(p);
    }

    lower.pop();
    upper.pop();
    return [...lower, ...upper];
}

const pts = [
    {x:0,y:0},{x:1,y:1},{x:2,y:2},{x:0,y:2},{x:2,y:0},{x:1,y:0}
];
const hull = convexHull(pts);
console.log(hull.map(p => `(${p.x},${p.y})`).join(" → "));
// (0,0) → (2,0) → (2,2) → (0,2)
console.log("Hull size:", hull.length);   // 4
JS,
            ],
            [
                'title'       => 'Segmented Sieve',
                'difficulty'  => 'hard',
                'description' => 'Find all primes in the range [L, R] using a segmented sieve. First find small primes up to √R with a simple sieve, then use them to mark composites in each segment.',
                'solution_code' => <<<'JS'
function segmentedSieve(L, R) {
    const limit = Math.floor(Math.sqrt(R));

    // Simple sieve up to sqrt(R)
    const smallPrimes = [];
    const isSmallPrime = new Array(limit + 1).fill(true);
    isSmallPrime[0] = isSmallPrime[1] = false;
    for (let i = 2; i <= limit; i++) {
        if (isSmallPrime[i]) {
            smallPrimes.push(i);
            for (let j = i * i; j <= limit; j += i) isSmallPrime[j] = false;
        }
    }

    // Segment sieve
    const size = R - L + 1;
    const isComposite = new Array(size).fill(false);
    if (L <= 1) isComposite[1 - L] = true;

    for (const p of smallPrimes) {
        let start = Math.max(p * p, Math.ceil(L / p) * p);
        if (start === p) start += p;
        for (let j = start; j <= R; j += p) isComposite[j - L] = true;
    }

    const primes = [];
    for (let i = 0; i < size; i++) {
        if (!isComposite[i] && L + i >= 2) primes.push(L + i);
    }
    return primes;
}

console.log(segmentedSieve(10, 30));
// [11, 13, 17, 19, 23, 29]
console.log(segmentedSieve(1, 20));
// [2, 3, 5, 7, 11, 13, 17, 19]
console.log(segmentedSieve(900, 1000).length);   // 14
JS,
            ],
            [
                'title'       => 'Karatsuba Multiplication',
                'difficulty'  => 'hard',
                'description' => 'Implement the Karatsuba algorithm for multiplying two large integers (represented as BigInts) in O(n^1.585) instead of O(n²). Uses divide-and-conquer: xy = ac·10^2m + (ad+bc)·10^m + bd where ad+bc = (a+b)(c+d)−ac−bd.',
                'solution_code' => <<<'JS'
function karatsuba(x, y) {
    if (x < 100n || y < 100n) return x * y;

    const xStr = x.toString();
    const m = Math.floor(xStr.length / 2);
    const m2 = BigInt(10 ** m);

    const a = x / m2;
    const b = x % m2;
    const c = y / m2;
    const d = y % m2;

    const ac   = karatsuba(a, c);
    const bd   = karatsuba(b, d);
    const abcd = karatsuba(a + b, c + d) - ac - bd;

    return ac * (m2 * m2) + abcd * m2 + bd;
}

const x = 123456789n;
const y = 987654321n;
console.log(karatsuba(x, y).toString());   // 121932631112635269
console.log((x * y).toString());           // 121932631112635269 (verify)

// Larger numbers
const a = 9999999999999999n;
const b = 8888888888888888n;
console.log(karatsuba(a, b) === a * b);    // true
JS,
            ],
        ];
    }
}
