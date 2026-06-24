<?php

namespace Database\Seeders;

use App\Models\Problem;
use Illuminate\Database\Seeder;

class JavaScriptSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->problems() as $i => $p) {
            Problem::updateOrCreate(
                ['order_index' => $i + 1],
                array_merge($p, ['order_index' => $i + 1, 'category' => 'JavaScript'])
            );
        }

        $this->command->info('Seeded ' . count($this->problems()) . ' JavaScript problems (1–30).');
    }

    private function problems(): array
    {
        return [

            // ─── EASY ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Hello World',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the string "Hello, World!".',
                'solution_code' => <<<'JS'
function helloWorld() {
    return "Hello, World!";
}

console.log(helloWorld());    // "Hello, World!"
JS,
            ],
            [
                'title'       => 'Even or Odd',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns "Even" if the number is even and "Odd" if it is odd.',
                'solution_code' => <<<'JS'
function evenOrOdd(n) {
    return n % 2 === 0 ? "Even" : "Odd";
}

console.log(evenOrOdd(4));   // Even
console.log(evenOrOdd(7));   // Odd
console.log(evenOrOdd(0));   // Even
JS,
            ],
            [
                'title'       => 'FizzBuzz',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns "Fizz" for multiples of 3, "Buzz" for multiples of 5, "FizzBuzz" for multiples of both, and the number itself otherwise.',
                'solution_code' => <<<'JS'
function fizzBuzz(n) {
    if (n % 15 === 0) return "FizzBuzz";
    if (n % 3 === 0)  return "Fizz";
    if (n % 5 === 0)  return "Buzz";
    return String(n);
}

for (let i = 1; i <= 20; i++) {
    console.log(fizzBuzz(i));
}
JS,
            ],
            [
                'title'       => 'Reverse a String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that reverses a given string.',
                'solution_code' => <<<'JS'
function reverseString(str) {
    return str.split("").reverse().join("");
}

console.log(reverseString("hello"));    // "olleh"
console.log(reverseString("abcde"));    // "edcba"
console.log(reverseString(""));         // ""
JS,
            ],
            [
                'title'       => 'Palindrome Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a string reads the same forwards and backwards (case-insensitive, alphanumeric only).',
                'solution_code' => <<<'JS'
function isPalindrome(str) {
    const clean = str.toLowerCase().replace(/[^a-z0-9]/g, "");
    return clean === clean.split("").reverse().join("");
}

console.log(isPalindrome("racecar"));                         // true
console.log(isPalindrome("A man a plan a canal Panama"));     // true
console.log(isPalindrome("hello"));                           // false
JS,
            ],
            [
                'title'       => 'Count Vowels',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts the number of vowels (a, e, i, o, u) in a string.',
                'solution_code' => <<<'JS'
function countVowels(str) {
    return (str.toLowerCase().match(/[aeiou]/g) || []).length;
}

console.log(countVowels("hello"));         // 2
console.log(countVowels("JavaScript"));    // 3
console.log(countVowels("rhythm"));        // 0
JS,
            ],
            [
                'title'       => 'Check Anagram',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if two strings are anagrams of each other (same characters, different order).',
                'solution_code' => <<<'JS'
function isAnagram(a, b) {
    const normalize = str => str.toLowerCase().split("").sort().join("");
    return normalize(a) === normalize(b);
}

console.log(isAnagram("listen", "silent"));   // true
console.log(isAnagram("hello", "world"));     // false
JS,
            ],
            [
                'title'       => 'Remove Duplicates from Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that removes duplicate values from an array and returns a new array with unique elements.',
                'solution_code' => <<<'JS'
function removeDuplicates(arr) {
    return [...new Set(arr)];
}

console.log(removeDuplicates([1, 2, 2, 3, 4, 4, 5]));   // [1, 2, 3, 4, 5]
console.log(removeDuplicates(["a", "b", "a", "c"]));     // ["a", "b", "c"]
JS,
            ],
            [
                'title'       => 'Two Sum',
                'difficulty'  => 'easy',
                'description' => 'Given an array of integers and a target sum, return the indices of the two numbers that add up to the target.',
                'solution_code' => <<<'JS'
function twoSum(nums, target) {
    const map = new Map();
    for (let i = 0; i < nums.length; i++) {
        const complement = target - nums[i];
        if (map.has(complement)) return [map.get(complement), i];
        map.set(nums[i], i);
    }
    return [];
}

console.log(twoSum([2, 7, 11, 15], 9));   // [0, 1]
console.log(twoSum([3, 2, 4], 6));         // [1, 2]
console.log(twoSum([3, 3], 6));            // [0, 1]
JS,
            ],
            [
                'title'       => 'Deep Clone Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that creates a deep clone of a plain JavaScript object.',
                'solution_code' => <<<'JS'
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

const original = { a: 1, b: { c: 2, d: [3, 4] } };
const clone = deepClone(original);
clone.b.c = 99;

console.log(original.b.c);   // 2 (unchanged)
console.log(clone.b.c);      // 99
JS,
            ],

            // ─── MEDIUM ──────────────────────────────────────────────────────────────

            [
                'title'       => 'Valid Parentheses',
                'difficulty'  => 'medium',
                'description' => 'Given a string of brackets "()", "[]", "{}", return true if the string is valid (every opening bracket has a matching closing bracket in the correct order).',
                'solution_code' => <<<'JS'
function isValid(s) {
    const stack = [];
    const pairs = { ")": "(", "]": "[", "}": "{" };
    for (const ch of s) {
        if ("([{".includes(ch)) {
            stack.push(ch);
        } else {
            if (stack.pop() !== pairs[ch]) return false;
        }
    }
    return stack.length === 0;
}

console.log(isValid("()[]{}"));    // true
console.log(isValid("(]"));        // false
console.log(isValid("{[()]}"));    // true
JS,
            ],
            [
                'title'       => 'Merge Two Sorted Arrays',
                'difficulty'  => 'medium',
                'description' => 'Write a function that merges two sorted arrays into one sorted array.',
                'solution_code' => <<<'JS'
function mergeSorted(arr1, arr2) {
    const result = [];
    let i = 0, j = 0;
    while (i < arr1.length && j < arr2.length) {
        if (arr1[i] <= arr2[j]) result.push(arr1[i++]);
        else result.push(arr2[j++]);
    }
    while (i < arr1.length) result.push(arr1[i++]);
    while (j < arr2.length) result.push(arr2[j++]);
    return result;
}

console.log(mergeSorted([1, 3, 5], [2, 4, 6]));   // [1, 2, 3, 4, 5, 6]
console.log(mergeSorted([1, 2], [3, 4]));           // [1, 2, 3, 4]
JS,
            ],
            [
                'title'       => 'Longest Common Prefix',
                'difficulty'  => 'medium',
                'description' => 'Write a function that finds the longest common prefix string among an array of strings.',
                'solution_code' => <<<'JS'
function longestCommonPrefix(strs) {
    if (!strs.length) return "";
    let prefix = strs[0];
    for (let i = 1; i < strs.length; i++) {
        while (!strs[i].startsWith(prefix)) {
            prefix = prefix.slice(0, -1);
            if (!prefix) return "";
        }
    }
    return prefix;
}

console.log(longestCommonPrefix(["flower", "flow", "flight"]));   // "fl"
console.log(longestCommonPrefix(["dog", "racecar", "car"]));      // ""
JS,
            ],
            [
                'title'       => 'Group Anagrams',
                'difficulty'  => 'medium',
                'description' => 'Given an array of strings, group them by their anagram sets.',
                'solution_code' => <<<'JS'
function groupAnagrams(strs) {
    const map = new Map();
    for (const str of strs) {
        const key = str.split("").sort().join("");
        if (!map.has(key)) map.set(key, []);
        map.get(key).push(str);
    }
    return Array.from(map.values());
}

console.log(groupAnagrams(["eat","tea","tan","ate","nat","bat"]));
// [["eat","tea","ate"],["tan","nat"],["bat"]]
JS,
            ],
            [
                'title'       => 'Longest Palindromic Substring',
                'difficulty'  => 'medium',
                'description' => 'Given a string, return the longest palindromic substring.',
                'solution_code' => <<<'JS'
function longestPalindrome(s) {
    let start = 0, maxLen = 1;

    function expand(left, right) {
        while (left >= 0 && right < s.length && s[left] === s[right]) {
            if (right - left + 1 > maxLen) {
                start = left;
                maxLen = right - left + 1;
            }
            left--;
            right++;
        }
    }

    for (let i = 0; i < s.length; i++) {
        expand(i, i);       // odd length
        expand(i, i + 1);   // even length
    }
    return s.slice(start, start + maxLen);
}

console.log(longestPalindrome("babad"));    // "bab"
console.log(longestPalindrome("cbbd"));     // "bb"
console.log(longestPalindrome("racecar"));  // "racecar"
JS,
            ],
            [
                'title'       => 'Longest Substring Without Repeating Characters',
                'difficulty'  => 'medium',
                'description' => 'Find the length of the longest substring without repeating characters.',
                'solution_code' => <<<'JS'
function lengthOfLongestSubstring(s) {
    const seen = new Map();
    let max = 0, left = 0;
    for (let right = 0; right < s.length; right++) {
        if (seen.has(s[right]) && seen.get(s[right]) >= left) {
            left = seen.get(s[right]) + 1;
        }
        seen.set(s[right], right);
        max = Math.max(max, right - left + 1);
    }
    return max;
}

console.log(lengthOfLongestSubstring("abcabcbb"));   // 3
console.log(lengthOfLongestSubstring("bbbbb"));      // 1
console.log(lengthOfLongestSubstring("pwwkew"));     // 3
JS,
            ],
            [
                'title'       => 'Product of Array Except Self',
                'difficulty'  => 'medium',
                'description' => 'Given an array, return a new array where each element is the product of all other elements. Do not use division.',
                'solution_code' => <<<'JS'
function productExceptSelf(nums) {
    const n = nums.length;
    const result = new Array(n).fill(1);
    let prefix = 1;
    for (let i = 0; i < n; i++) {
        result[i] = prefix;
        prefix *= nums[i];
    }
    let suffix = 1;
    for (let i = n - 1; i >= 0; i--) {
        result[i] *= suffix;
        suffix *= nums[i];
    }
    return result;
}

console.log(productExceptSelf([1, 2, 3, 4]));   // [24, 12, 8, 6]
JS,
            ],
            [
                'title'       => 'Container with Most Water',
                'difficulty'  => 'medium',
                'description' => 'Given an array of heights representing walls, find two walls that together hold the most water.',
                'solution_code' => <<<'JS'
function maxArea(height) {
    let left = 0, right = height.length - 1, max = 0;
    while (left < right) {
        const water = Math.min(height[left], height[right]) * (right - left);
        max = Math.max(max, water);
        if (height[left] < height[right]) left++;
        else right--;
    }
    return max;
}

console.log(maxArea([1, 8, 6, 2, 5, 4, 8, 3, 7]));   // 49
console.log(maxArea([1, 1]));                           // 1
JS,
            ],
            [
                'title'       => 'Three Sum',
                'difficulty'  => 'medium',
                'description' => 'Find all unique triplets in an array that sum to zero.',
                'solution_code' => <<<'JS'
function threeSum(nums) {
    nums.sort((a, b) => a - b);
    const result = [];
    for (let i = 0; i < nums.length - 2; i++) {
        if (i > 0 && nums[i] === nums[i - 1]) continue;
        let left = i + 1, right = nums.length - 1;
        while (left < right) {
            const sum = nums[i] + nums[left] + nums[right];
            if (sum === 0) {
                result.push([nums[i], nums[left], nums[right]]);
                while (nums[left] === nums[left + 1]) left++;
                while (nums[right] === nums[right - 1]) right--;
                left++; right--;
            } else if (sum < 0) left++;
            else right--;
        }
    }
    return result;
}

console.log(JSON.stringify(threeSum([-1, 0, 1, 2, -1, -4])));
// [[-1,-1,2],[-1,0,1]]
JS,
            ],
            [
                'title'       => 'Roman to Integer',
                'difficulty'  => 'medium',
                'description' => 'Convert a Roman numeral string to an integer.',
                'solution_code' => <<<'JS'
function romanToInt(s) {
    const map = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 };
    let result = 0;
    for (let i = 0; i < s.length; i++) {
        const curr = map[s[i]];
        const next = map[s[i + 1]];
        if (next && curr < next) result -= curr;
        else result += curr;
    }
    return result;
}

console.log(romanToInt("III"));      // 3
console.log(romanToInt("LVIII"));    // 58
console.log(romanToInt("MCMXCIV")); // 1994
JS,
            ],

            // ─── HARD ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Implement Promise.all',
                'difficulty'  => 'hard',
                'description' => 'Implement your own version of Promise.all. It should return a promise that resolves with an array of all resolved values, or rejects as soon as any promise rejects.',
                'solution_code' => <<<'JS'
function promiseAll(promises) {
    return new Promise((resolve, reject) => {
        if (promises.length === 0) return resolve([]);
        const results = new Array(promises.length);
        let remaining = promises.length;
        promises.forEach((p, i) => {
            Promise.resolve(p).then(val => {
                results[i] = val;
                if (--remaining === 0) resolve(results);
            }).catch(reject);
        });
    });
}

promiseAll([
    Promise.resolve(1),
    Promise.resolve(2),
    Promise.resolve(3),
]).then(vals => console.log(vals));    // [1, 2, 3]

promiseAll([
    Promise.resolve("a"),
    Promise.reject("error"),
]).catch(err => console.log(err));     // "error"
JS,
            ],
            [
                'title'       => 'Debounce',
                'difficulty'  => 'hard',
                'description' => 'Implement a debounce function. It should return a new function that delays invoking the original function until after `wait` ms have elapsed since the last time it was called.',
                'solution_code' => <<<'JS'
function debounce(fn, wait) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), wait);
    };
}

let callCount = 0;
const debouncedFn = debounce(() => {
    callCount++;
    console.log("Called! Count:", callCount);
}, 100);

debouncedFn();
debouncedFn();
debouncedFn();
// Only logs once after 100ms: "Called! Count: 1"
setTimeout(() => console.log("Final count:", callCount), 200);   // 1
JS,
            ],
            [
                'title'       => 'Throttle',
                'difficulty'  => 'hard',
                'description' => 'Implement a throttle function. It should return a new function that invokes the original at most once per `limit` ms, no matter how many times it is called.',
                'solution_code' => <<<'JS'
function throttle(fn, limit) {
    let lastCall = 0;
    return function(...args) {
        const now = Date.now();
        if (now - lastCall >= limit) {
            lastCall = now;
            return fn.apply(this, args);
        }
    };
}

let count = 0;
const throttled = throttle(() => { count++; }, 100);

throttled();   // executes (count = 1)
throttled();   // ignored — within 100ms
throttled();   // ignored

setTimeout(() => {
    throttled();   // executes (count = 2)
    console.log("Count:", count);   // Count: 2
}, 150);
JS,
            ],
            [
                'title'       => 'EventEmitter',
                'difficulty'  => 'hard',
                'description' => 'Implement an EventEmitter class with on(event, listener), off(event, listener), emit(event, ...args), and once(event, listener) methods.',
                'solution_code' => <<<'JS'
class EventEmitter {
    constructor() {
        this.events = {};
    }

    on(event, listener) {
        if (!this.events[event]) this.events[event] = [];
        this.events[event].push(listener);
        return this;
    }

    off(event, listener) {
        if (!this.events[event]) return this;
        this.events[event] = this.events[event].filter(l => l !== listener);
        return this;
    }

    emit(event, ...args) {
        (this.events[event] || []).forEach(l => l(...args));
        return this;
    }

    once(event, listener) {
        const wrapper = (...args) => {
            listener(...args);
            this.off(event, wrapper);
        };
        return this.on(event, wrapper);
    }
}

const emitter = new EventEmitter();

emitter.on("data", val => console.log("on:", val));
emitter.once("data", val => console.log("once:", val));

emitter.emit("data", "hello");   // on: hello  |  once: hello
emitter.emit("data", "world");   // on: world  (once does not fire again)
JS,
            ],
            [
                'title'       => 'Deep Clone with Circular References',
                'difficulty'  => 'hard',
                'description' => 'Implement a deep clone function that correctly handles circular references without causing infinite recursion.',
                'solution_code' => <<<'JS'
function deepClone(obj, seen = new WeakMap()) {
    if (obj === null || typeof obj !== "object") return obj;
    if (seen.has(obj)) return seen.get(obj);

    const clone = Array.isArray(obj) ? [] : {};
    seen.set(obj, clone);

    for (const key of Object.keys(obj)) {
        clone[key] = deepClone(obj[key], seen);
    }
    return clone;
}

const a = { x: 1, b: { y: 2 } };
a.self = a;    // circular reference

const cloned = deepClone(a);
console.log(cloned.x);          // 1
console.log(cloned.b.y);        // 2
console.log(cloned.self === cloned);   // true (circular preserved)
console.log(cloned === a);             // false (different object)
JS,
            ],
            [
                'title'       => 'Curry (N-ary)',
                'difficulty'  => 'hard',
                'description' => 'Implement a generic curry function that transforms a function of any arity into its curried form. It should support both partial application and normal full calls.',
                'solution_code' => <<<'JS'
function curry(fn) {
    return function curried(...args) {
        if (args.length >= fn.length) {
            return fn(...args);
        }
        return (...moreArgs) => curried(...args, ...moreArgs);
    };
}

const add = curry((a, b, c) => a + b + c);

console.log(add(1)(2)(3));       // 6
console.log(add(1, 2)(3));       // 6
console.log(add(1)(2, 3));       // 6
console.log(add(1, 2, 3));       // 6

const multiply = curry((a, b) => a * b);
const double = multiply(2);
console.log(double(5));    // 10
console.log(double(10));   // 20
JS,
            ],
            [
                'title'       => 'Lazy Range Generator',
                'difficulty'  => 'hard',
                'description' => 'Implement a lazy range using a JavaScript generator. It should produce integers from start to end with an optional step, only computing each value on demand.',
                'solution_code' => <<<'JS'
function* range(start, end, step = 1) {
    for (let i = start; i <= end; i += step) {
        yield i;
    }
}

console.log([...range(1, 5)]);           // [1, 2, 3, 4, 5]
console.log([...range(0, 10, 2)]);       // [0, 2, 4, 6, 8, 10]

// Lazy: only take what you need
function take(gen, n) {
    const result = [];
    for (const val of gen) {
        result.push(val);
        if (result.length >= n) break;
    }
    return result;
}

console.log(take(range(1, Infinity), 5));   // [1, 2, 3, 4, 5]
JS,
            ],
            [
                'title'       => 'Proxy-based Observable Object',
                'difficulty'  => 'hard',
                'description' => 'Use JavaScript Proxy to create an observable object that calls registered listeners whenever a property is set, passing the key, new value, and old value.',
                'solution_code' => <<<'JS'
function createObservable(target) {
    const listeners = [];

    const proxy = new Proxy(target, {
        set(obj, key, value) {
            const oldValue = obj[key];
            obj[key] = value;
            listeners.forEach(fn => fn(key, value, oldValue));
            return true;
        },
    });

    proxy.$on = (fn) => listeners.push(fn);
    return proxy;
}

const state = createObservable({ count: 0, name: "Alice" });

state.$on((key, newVal, oldVal) => {
    console.log(`${key}: ${oldVal} → ${newVal}`);
});

state.count = 1;     // count: 0 → 1
state.count = 2;     // count: 1 → 2
state.name = "Bob";  // name: Alice → Bob
JS,
            ],
            [
                'title'       => 'Async Task Queue',
                'difficulty'  => 'hard',
                'description' => 'Implement an async task queue that runs tasks (async functions) with a configurable concurrency limit. Tasks beyond the limit wait until a running task finishes.',
                'solution_code' => <<<'JS'
function createQueue(concurrency) {
    let running = 0;
    const queue = [];

    function next() {
        while (running < concurrency && queue.length > 0) {
            const { task, resolve, reject } = queue.shift();
            running++;
            task()
                .then(resolve)
                .catch(reject)
                .finally(() => {
                    running--;
                    next();
                });
        }
    }

    return function enqueue(task) {
        return new Promise((resolve, reject) => {
            queue.push({ task, resolve, reject });
            next();
        });
    };
}

const queue = createQueue(2);   // max 2 concurrent tasks

function delay(ms, label) {
    return () => new Promise(resolve => {
        setTimeout(() => { console.log(`Done: ${label}`); resolve(label); }, ms);
    });
}

queue(delay(100, "A"));
queue(delay(50,  "B"));
queue(delay(30,  "C"));   // waits until A or B finishes
// Output order: B (50ms), A (100ms), C (130ms)
JS,
            ],
            [
                'title'       => 'Flatten Without flat()',
                'difficulty'  => 'hard',
                'description' => 'Implement a function that flattens a deeply nested array to any specified depth without using the built-in Array.prototype.flat(). Use an iterative approach.',
                'solution_code' => <<<'JS'
function flattenDeep(arr, depth = Infinity) {
    const result = [];
    const stack = arr.map(item => [item, depth]);

    while (stack.length) {
        const [item, d] = stack.pop();
        if (Array.isArray(item) && d > 0) {
            for (let i = item.length - 1; i >= 0; i--) {
                stack.push([item[i], d - 1]);
            }
        } else {
            result.push(item);
        }
    }
    return result;
}

console.log(flattenDeep([1, [2, [3, [4, [5]]]]]));
// [1, 2, 3, 4, 5]

console.log(flattenDeep([1, [2, [3, [4]]]], 2));
// [1, 2, 3, [4]]  (depth=2)

console.log(flattenDeep([[1, 2], [3, [4, 5]]]));
// [1, 2, 3, 4, 5]
JS,
            ],
        ];
    }
}
