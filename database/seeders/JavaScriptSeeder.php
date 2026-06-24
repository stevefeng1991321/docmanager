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

        $this->command->info('Seeded ' . count($this->problems()) . ' JavaScript problems (1–129).');
    }

    private function problems(): array
    {
        return [

            // ─── BASICS ─────────────────────────────────────────────────────────────

            [
                'title'       => 'Hello World',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the string "Hello, World!".',
                'solution_code' => <<<'JS'
function helloWorld() {
    return "Hello, World!";
}

console.log(helloWorld());
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

            // ─── STRINGS (EASY) ──────────────────────────────────────────────────────

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

console.log(isPalindrome("racecar"));        // true
console.log(isPalindrome("A man a plan a canal Panama")); // true
console.log(isPalindrome("hello"));          // false
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
                'title'       => 'Capitalize Words',
                'difficulty'  => 'easy',
                'description' => 'Write a function that capitalizes the first letter of each word in a sentence.',
                'solution_code' => <<<'JS'
function titleCase(str) {
    return str
        .split(" ")
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(" ");
}

console.log(titleCase("hello world"));           // "Hello World"
console.log(titleCase("the quick brown fox"));   // "The Quick Brown Fox"
JS,
            ],
            [
                'title'       => 'Count Character Occurrences',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns an object with each character and its count in the string.',
                'solution_code' => <<<'JS'
function charCount(str) {
    const result = {};
    for (const ch of str) {
        result[ch] = (result[ch] || 0) + 1;
    }
    return result;
}

console.log(charCount("hello"));   // { h:1, e:1, l:2, o:1 }
console.log(charCount("aabbc"));   // { a:2, b:2, c:1 }
JS,
            ],
            [
                'title'       => 'Truncate String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that truncates a string to a given length and appends "..." if it was truncated.',
                'solution_code' => <<<'JS'
function truncate(str, maxLength) {
    if (str.length <= maxLength) return str;
    return str.slice(0, maxLength) + "...";
}

console.log(truncate("Hello World", 5));    // "Hello..."
console.log(truncate("Hi", 10));            // "Hi"
JS,
            ],

            // ─── ARRAYS (EASY) ───────────────────────────────────────────────────────

            [
                'title'       => 'Remove Duplicates from Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that removes duplicate values from an array and returns a new array with unique elements.',
                'solution_code' => <<<'JS'
function removeDuplicates(arr) {
    return [...new Set(arr)];
}

console.log(removeDuplicates([1, 2, 2, 3, 4, 4, 5]));  // [1, 2, 3, 4, 5]
console.log(removeDuplicates(["a", "b", "a", "c"]));    // ["a", "b", "c"]
JS,
            ],
            [
                'title'       => 'Sum of Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the sum of all numbers in an array.',
                'solution_code' => <<<'JS'
function arraySum(arr) {
    return arr.reduce((total, n) => total + n, 0);
}

console.log(arraySum([1, 2, 3, 4, 5]));   // 15
console.log(arraySum([-1, 0, 1]));         // 0
console.log(arraySum([]));                 // 0
JS,
            ],
            [
                'title'       => 'Max in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the maximum value in an array of numbers.',
                'solution_code' => <<<'JS'
function maxInArray(arr) {
    return Math.max(...arr);
}

console.log(maxInArray([3, 1, 4, 1, 5, 9, 2]));   // 9
console.log(maxInArray([-3, -1, -7]));              // -1
JS,
            ],
            [
                'title'       => 'Min in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the minimum value in an array of numbers.',
                'solution_code' => <<<'JS'
function minInArray(arr) {
    return Math.min(...arr);
}

console.log(minInArray([3, 1, 4, 1, 5, 9, 2]));   // 1
console.log(minInArray([-3, -1, -7]));              // -7
JS,
            ],
            [
                'title'       => 'Sort Array Ascending',
                'difficulty'  => 'easy',
                'description' => 'Write a function that sorts an array of numbers in ascending order.',
                'solution_code' => <<<'JS'
function sortAscending(arr) {
    return [...arr].sort((a, b) => a - b);
}

console.log(sortAscending([3, 1, 4, 1, 5, 9, 2]));  // [1, 1, 2, 3, 4, 5, 9]
console.log(sortAscending([10, 2, 8, 4]));            // [2, 4, 8, 10]
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
        if (map.has(complement)) {
            return [map.get(complement), i];
        }
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
                'title'       => 'Find Missing Number',
                'difficulty'  => 'easy',
                'description' => 'Given an array containing n-1 numbers from 1 to n with one missing, find the missing number.',
                'solution_code' => <<<'JS'
function missingNumber(arr) {
    const n = arr.length + 1;
    const expectedSum = (n * (n + 1)) / 2;
    const actualSum = arr.reduce((sum, x) => sum + x, 0);
    return expectedSum - actualSum;
}

console.log(missingNumber([1, 2, 4, 5, 6]));   // 3
console.log(missingNumber([3, 7, 1, 2, 8, 4, 5])); // 6
JS,
            ],
            [
                'title'       => 'Reverse Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a reversed copy of an array without modifying the original.',
                'solution_code' => <<<'JS'
function reverseArray(arr) {
    return [...arr].reverse();
}

console.log(reverseArray([1, 2, 3, 4, 5]));   // [5, 4, 3, 2, 1]
console.log(reverseArray(["a", "b", "c"]));    // ["c", "b", "a"]
JS,
            ],
            [
                'title'       => 'Rotate Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that rotates an array to the right by k steps.',
                'solution_code' => <<<'JS'
function rotateArray(arr, k) {
    const n = arr.length;
    if (n === 0) return arr;
    const steps = k % n;
    return [...arr.slice(n - steps), ...arr.slice(0, n - steps)];
}

console.log(rotateArray([1, 2, 3, 4, 5], 2));   // [4, 5, 1, 2, 3]
console.log(rotateArray([1, 2, 3], 4));          // [3, 1, 2]
JS,
            ],
            [
                'title'       => 'Move Zeros to End',
                'difficulty'  => 'easy',
                'description' => 'Write a function that moves all zeros to the end of an array while maintaining the relative order of non-zero elements.',
                'solution_code' => <<<'JS'
function moveZeros(arr) {
    const nonZeros = arr.filter(x => x !== 0);
    const zeros = arr.filter(x => x === 0);
    return [...nonZeros, ...zeros];
}

console.log(moveZeros([0, 1, 0, 3, 12]));     // [1, 3, 12, 0, 0]
console.log(moveZeros([0, 0, 1]));             // [1, 0, 0]
JS,
            ],
            [
                'title'       => 'Array Intersection',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the intersection of two arrays (elements that appear in both).',
                'solution_code' => <<<'JS'
function intersection(arr1, arr2) {
    const set = new Set(arr2);
    return [...new Set(arr1.filter(x => set.has(x)))];
}

console.log(intersection([1, 2, 3, 4], [3, 4, 5, 6]));     // [3, 4]
console.log(intersection(["a", "b"], ["b", "c", "a"]));     // ["a", "b"]
JS,
            ],
            [
                'title'       => 'Chunk Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that splits an array into chunks of a given size.',
                'solution_code' => <<<'JS'
function chunkArray(arr, size) {
    const result = [];
    for (let i = 0; i < arr.length; i += size) {
        result.push(arr.slice(i, i + size));
    }
    return result;
}

console.log(chunkArray([1, 2, 3, 4, 5], 2));   // [[1,2],[3,4],[5]]
console.log(chunkArray([1, 2, 3, 4, 5, 6], 3)); // [[1,2,3],[4,5,6]]
JS,
            ],
            [
                'title'       => 'Count Occurrences in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts how many times a value appears in an array.',
                'solution_code' => <<<'JS'
function countOccurrences(arr, val) {
    return arr.filter(x => x === val).length;
}

console.log(countOccurrences([1, 2, 2, 3, 2], 2));       // 3
console.log(countOccurrences(["a", "b", "a", "c"], "a")); // 2
JS,
            ],
            [
                'title'       => 'Flatten Array (One Level)',
                'difficulty'  => 'easy',
                'description' => 'Write a function that flattens a one-level nested array.',
                'solution_code' => <<<'JS'
function flattenOne(arr) {
    return arr.flat();
}

console.log(flattenOne([[1, 2], [3, 4], [5]]));       // [1, 2, 3, 4, 5]
console.log(flattenOne([["a", "b"], ["c"]]));          // ["a", "b", "c"]
JS,
            ],
            [
                'title'       => 'Product of Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the product of all numbers in an array.',
                'solution_code' => <<<'JS'
function arrayProduct(arr) {
    return arr.reduce((product, n) => product * n, 1);
}

console.log(arrayProduct([1, 2, 3, 4]));   // 24
console.log(arrayProduct([5, 5]));          // 25
console.log(arrayProduct([]));              // 1
JS,
            ],
            [
                'title'       => 'Max Consecutive Ones',
                'difficulty'  => 'easy',
                'description' => 'Given a binary array (containing only 0s and 1s), find the maximum number of consecutive 1s.',
                'solution_code' => <<<'JS'
function maxConsecutiveOnes(nums) {
    let max = 0, current = 0;
    for (const n of nums) {
        current = n === 1 ? current + 1 : 0;
        max = Math.max(max, current);
    }
    return max;
}

console.log(maxConsecutiveOnes([1, 1, 0, 1, 1, 1]));   // 3
console.log(maxConsecutiveOnes([1, 0, 1, 1, 0, 1]));   // 2
JS,
            ],

            // ─── OBJECTS (EASY) ──────────────────────────────────────────────────────

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
            [
                'title'       => 'Merge Objects',
                'difficulty'  => 'easy',
                'description' => "Write a function that merges two objects, with the second object's values overriding the first on key conflicts.",
                'solution_code' => <<<'JS'
function mergeObjects(obj1, obj2) {
    return { ...obj1, ...obj2 };
}

const a = { x: 1, y: 2 };
const b = { y: 10, z: 3 };
console.log(mergeObjects(a, b));   // { x: 1, y: 10, z: 3 }
JS,
            ],
            [
                'title'       => 'Filter Array by Condition',
                'difficulty'  => 'easy',
                'description' => 'Write a function that filters an array, keeping only numbers greater than a given threshold.',
                'solution_code' => <<<'JS'
function filterGreaterThan(arr, threshold) {
    return arr.filter(n => n > threshold);
}

console.log(filterGreaterThan([1, 5, 3, 8, 2, 9], 4));   // [5, 8, 9]
console.log(filterGreaterThan([10, 20, 30], 25));          // [30]
JS,
            ],
            [
                'title'       => 'Map Array to Squares',
                'difficulty'  => 'easy',
                'description' => 'Write a function that takes an array of numbers and returns a new array with each number squared.',
                'solution_code' => <<<'JS'
function squareArray(arr) {
    return arr.map(n => n * n);
}

console.log(squareArray([1, 2, 3, 4, 5]));   // [1, 4, 9, 16, 25]
console.log(squareArray([-2, 0, 3]));          // [4, 0, 9]
JS,
            ],

            // ─── STRINGS (MEDIUM) ────────────────────────────────────────────────────

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

console.log(longestCommonPrefix(["flower", "flow", "flight"]));  // "fl"
console.log(longestCommonPrefix(["dog", "racecar", "car"]));     // ""
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

console.log(romanToInt("III"));     // 3
console.log(romanToInt("LVIII"));   // 58
console.log(romanToInt("MCMXCIV")); // 1994
JS,
            ],
            [
                'title'       => 'Integer to Roman',
                'difficulty'  => 'medium',
                'description' => 'Convert an integer (1–3999) to a Roman numeral string.',
                'solution_code' => <<<'JS'
function intToRoman(num) {
    const values  = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
    const symbols = ["M","CM","D","CD","C","XC","L","XL","X","IX","V","IV","I"];
    let result = "";
    for (let i = 0; i < values.length; i++) {
        while (num >= values[i]) {
            result += symbols[i];
            num -= values[i];
        }
    }
    return result;
}

console.log(intToRoman(3));     // "III"
console.log(intToRoman(1994));  // "MCMXCIV"
console.log(intToRoman(58));    // "LVIII"
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

console.log(longestPalindrome("babad"));   // "bab"
console.log(longestPalindrome("cbbd"));    // "bb"
console.log(longestPalindrome("racecar")); // "racecar"
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

console.log(maxArea([1, 8, 6, 2, 5, 4, 8, 3, 7]));  // 49
console.log(maxArea([1, 1]));                          // 1
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

            // ─── STRINGS (from EasyProblemSeeder) ────────────────────────────────────

            [
                'title'       => 'Count Consonants',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts the number of consonants in a string.',
                'solution_code' => <<<'JS'
function countConsonants(str) {
    return (str.toLowerCase().match(/[bcdfghjklmnpqrstvwxyz]/g) || []).length;
}

console.log(countConsonants("hello"));        // 3
console.log(countConsonants("JavaScript"));   // 6
console.log(countConsonants("aeiou"));        // 0
JS,
            ],
            [
                'title'       => 'String Contains Only Digits',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a string contains only digit characters.',
                'solution_code' => <<<'JS'
function isAllDigits(str) {
    return str.length > 0 && /^\d+$/.test(str);
}

console.log(isAllDigits("12345"));    // true
console.log(isAllDigits("123a5"));    // false
console.log(isAllDigits(""));         // false
JS,
            ],
            [
                'title'       => 'Check All Uppercase',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if all alphabetic characters in a string are uppercase.',
                'solution_code' => <<<'JS'
function isAllUppercase(str) {
    return str === str.toUpperCase() && /[A-Z]/.test(str);
}

console.log(isAllUppercase("HELLO"));     // true
console.log(isAllUppercase("HELLo"));     // false
console.log(isAllUppercase("HELLO 123")); // true
JS,
            ],
            [
                'title'       => 'Check All Lowercase',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if all alphabetic characters in a string are lowercase.',
                'solution_code' => <<<'JS'
function isAllLowercase(str) {
    return str === str.toLowerCase() && /[a-z]/.test(str);
}

console.log(isAllLowercase("hello"));     // true
console.log(isAllLowercase("Hello"));     // false
console.log(isAllLowercase("hello 123")); // true
JS,
            ],
            [
                'title'       => 'Remove All Spaces',
                'difficulty'  => 'easy',
                'description' => 'Write a function that removes all whitespace characters from a string.',
                'solution_code' => <<<'JS'
function removeSpaces(str) {
    return str.replace(/\s/g, "");
}

console.log(removeSpaces("hello world"));          // "helloworld"
console.log(removeSpaces("  spaces  everywhere")); // "spaceseverywhere"
console.log(removeSpaces("nospaces"));             // "nospaces"
JS,
            ],
            [
                'title'       => 'Find First Non-Repeating Character',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the first character in a string that does not repeat, or null if all repeat.',
                'solution_code' => <<<'JS'
function firstUnique(str) {
    const count = {};
    for (const ch of str) count[ch] = (count[ch] || 0) + 1;
    for (const ch of str) if (count[ch] === 1) return ch;
    return null;
}

console.log(firstUnique("aabbcde"));    // "c"
console.log(firstUnique("aabb"));       // null
console.log(firstUnique("leetcode"));   // "l"
JS,
            ],
            [
                'title'       => 'Count Words in String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts the number of words in a string (words are separated by spaces).',
                'solution_code' => <<<'JS'
function wordCount(str) {
    const trimmed = str.trim();
    if (!trimmed) return 0;
    return trimmed.split(/\s+/).length;
}

console.log(wordCount("hello world"));          // 2
console.log(wordCount("  one  two   three  ")); // 3
console.log(wordCount(""));                     // 0
JS,
            ],
            [
                'title'       => 'Longest Word in Sentence',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the longest word in a sentence.',
                'solution_code' => <<<'JS'
function longestWord(sentence) {
    return sentence.split(" ").reduce((longest, word) =>
        word.length > longest.length ? word : longest, "");
}

console.log(longestWord("The quick brown fox"));          // "quick"
console.log(longestWord("JavaScript is awesome"));        // "JavaScript"
JS,
            ],
            [
                'title'       => 'Shortest Word in Sentence',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the shortest word in a sentence.',
                'solution_code' => <<<'JS'
function shortestWord(sentence) {
    return sentence.split(" ").reduce((shortest, word) =>
        word.length < shortest.length ? word : shortest);
}

console.log(shortestWord("The quick brown fox"));   // "The"
console.log(shortestWord("I love JavaScript"));     // "I"
JS,
            ],
            [
                'title'       => 'Reverse Words in Sentence',
                'difficulty'  => 'easy',
                'description' => 'Write a function that reverses the order of words in a sentence.',
                'solution_code' => <<<'JS'
function reverseWords(str) {
    return str.trim().split(/\s+/).reverse().join(" ");
}

console.log(reverseWords("Hello World"));           // "World Hello"
console.log(reverseWords("The quick brown fox"));   // "fox brown quick The"
JS,
            ],
            [
                'title'       => 'Remove Vowels from String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that removes all vowels from a string.',
                'solution_code' => <<<'JS'
function removeVowels(str) {
    return str.replace(/[aeiouAEIOU]/g, "");
}

console.log(removeVowels("hello world"));   // "hll wrld"
console.log(removeVowels("JavaScript"));    // "JvScrpt"
console.log(removeVowels("aeiou"));         // ""
JS,
            ],
            [
                'title'       => 'Mask a String',
                'difficulty'  => 'easy',
                'description' => 'Replace all but the last n characters of a string with "*" (useful for masking sensitive data like credit cards).',
                'solution_code' => <<<'JS'
function maskString(str, n = 4) {
    if (str.length <= n) return str;
    return "*".repeat(str.length - n) + str.slice(-n);
}

console.log(maskString("4111111111111234"));  // "************1234"
console.log(maskString("abc", 4));           // "abc"
JS,
            ],
            [
                'title'       => 'Check Alphanumeric String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a string contains only letters and numbers.',
                'solution_code' => <<<'JS'
function isAlphanumeric(str) {
    return /^[a-zA-Z0-9]+$/.test(str);
}

console.log(isAlphanumeric("Hello123"));   // true
console.log(isAlphanumeric("Hello 123")); // false (space)
console.log(isAlphanumeric("hello!"));    // false
JS,
            ],
            [
                'title'       => 'Repeat String N Times',
                'difficulty'  => 'easy',
                'description' => 'Write a function that repeats a string a given number of times.',
                'solution_code' => <<<'JS'
function repeatStr(str, n) {
    return str.repeat(Math.max(0, n));
}

console.log(repeatStr("ab", 3));    // "ababab"
console.log(repeatStr("ha", 5));    // "hahahahaha"
console.log(repeatStr("x", 0));     // ""
JS,
            ],
            [
                'title'       => 'Pad String to Length',
                'difficulty'  => 'easy',
                'description' => 'Write a function that pads a string on the left with a given character until it reaches the target length.',
                'solution_code' => <<<'JS'
function padLeft(str, length, char = "0") {
    return String(str).padStart(length, char);
}

console.log(padLeft("5", 3));          // "005"
console.log(padLeft("hello", 8, " ")); // "   hello"
console.log(padLeft("42", 5, "0"));    // "00042"
JS,
            ],
            [
                'title'       => 'Extract Numbers from String',
                'difficulty'  => 'easy',
                'description' => 'Write a function that extracts all numbers from a string and returns them as an array.',
                'solution_code' => <<<'JS'
function extractNumbers(str) {
    return (str.match(/-?\d+(\.\d+)?/g) || []).map(Number);
}

console.log(extractNumbers("I have 3 cats and 2 dogs"));  // [3, 2]
console.log(extractNumbers("Price: $12.5 and $4.99"));    // [12.5, 4.99]
console.log(extractNumbers("No numbers here!"));           // []
JS,
            ],
            [
                'title'       => 'Capitalize Only First Character',
                'difficulty'  => 'easy',
                'description' => 'Capitalize only the very first character of a string, leaving the rest unchanged.',
                'solution_code' => <<<'JS'
function capitalizeFirst(str) {
    if (!str) return str;
    return str.charAt(0).toUpperCase() + str.slice(1);
}

console.log(capitalizeFirst("hello world"));    // "Hello world"
console.log(capitalizeFirst("javaScript"));     // "JavaScript"
console.log(capitalizeFirst(""));               // ""
JS,
            ],
            [
                'title'       => 'String to Integer (Without parseInt)',
                'difficulty'  => 'easy',
                'description' => 'Convert a string of digits to an integer without using parseInt or Number().',
                'solution_code' => <<<'JS'
function strToInt(str) {
    let result = 0;
    const sign = str[0] === "-" ? -1 : 1;
    const digits = sign === -1 ? str.slice(1) : str;
    for (const ch of digits) {
        result = result * 10 + (ch.charCodeAt(0) - "0".charCodeAt(0));
    }
    return sign * result;
}

console.log(strToInt("1234"));    // 1234
console.log(strToInt("-567"));    // -567
console.log(strToInt("0"));       // 0
JS,
            ],
            [
                'title'       => 'Check String Starts With',
                'difficulty'  => 'easy',
                'description' => 'Write a function that checks if a string starts with a given prefix (case-sensitive).',
                'solution_code' => <<<'JS'
function startsWith(str, prefix) {
    return str.indexOf(prefix) === 0;
}

console.log(startsWith("Hello World", "Hello"));   // true
console.log(startsWith("Hello World", "World"));   // false
console.log(startsWith("JavaScript", "Java"));     // true
JS,
            ],
            [
                'title'       => 'Check String Ends With',
                'difficulty'  => 'easy',
                'description' => 'Write a function that checks if a string ends with a given suffix.',
                'solution_code' => <<<'JS'
function endsWith(str, suffix) {
    return str.slice(-suffix.length) === suffix;
}

console.log(endsWith("Hello World", "World"));   // true
console.log(endsWith("Hello World", "Hello"));   // false
console.log(endsWith("index.html", ".html"));    // true
JS,
            ],
            [
                'title'       => 'Case-Insensitive String Comparison',
                'difficulty'  => 'easy',
                'description' => 'Write a function that compares two strings for equality regardless of case.',
                'solution_code' => <<<'JS'
function equalsIgnoreCase(a, b) {
    return a.toLowerCase() === b.toLowerCase();
}

console.log(equalsIgnoreCase("Hello", "hello"));    // true
console.log(equalsIgnoreCase("WORLD", "world"));    // true
console.log(equalsIgnoreCase("foo", "bar"));        // false
JS,
            ],
            [
                'title'       => 'Clamp String Length',
                'difficulty'  => 'easy',
                'description' => 'Write a function that ensures a string is no longer than maxLength characters, appending "…" if it is cut.',
                'solution_code' => <<<'JS'
function clampString(str, maxLength) {
    if (str.length <= maxLength) return str;
    return str.slice(0, maxLength - 1) + "…";
}

console.log(clampString("Hello, World!", 8));    // "Hello, …"
console.log(clampString("Hi", 10));              // "Hi"
JS,
            ],
            [
                'title'       => 'Word Frequency Counter',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns an object with the count of each word in a sentence (case-insensitive, ignoring punctuation).',
                'solution_code' => <<<'JS'
function wordFrequency(sentence) {
    const words = sentence.toLowerCase().replace(/[^a-z\s]/g, "").split(/\s+/);
    return words.reduce((freq, word) => {
        if (word) freq[word] = (freq[word] || 0) + 1;
        return freq;
    }, {});
}

const result = wordFrequency("the cat sat on the mat and the cat");
console.log(result);
// { the: 3, cat: 2, sat: 1, on: 1, mat: 1, and: 1 }
JS,
            ],
            [
                'title'       => 'Caesar Cipher',
                'difficulty'  => 'easy',
                'description' => 'Implement the Caesar cipher: shift each letter in the string by n positions in the alphabet, wrapping around.',
                'solution_code' => <<<'JS'
function caesarCipher(str, shift) {
    return str.replace(/[a-zA-Z]/g, ch => {
        const base = ch >= "a" ? 97 : 65;
        return String.fromCharCode(((ch.charCodeAt(0) - base + shift) % 26) + base);
    });
}

console.log(caesarCipher("Hello", 3));    // "Khoor"
console.log(caesarCipher("xyz", 3));      // "abc"
console.log(caesarCipher("ABC", 1));      // "BCD"
JS,
            ],
            [
                'title'       => 'Number to Words (1–19)',
                'difficulty'  => 'easy',
                'description' => 'Convert an integer from 1 to 19 to its English word representation.',
                'solution_code' => <<<'JS'
function numberToWord(n) {
    const words = [
        "", "one", "two", "three", "four", "five", "six", "seven",
        "eight", "nine", "ten", "eleven", "twelve", "thirteen",
        "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"
    ];
    return words[n] || "out of range";
}

console.log(numberToWord(1));    // "one"
console.log(numberToWord(13));   // "thirteen"
console.log(numberToWord(19));   // "nineteen"
JS,
            ],

            // ─── ARRAYS (from EasyProblemSeeder) ─────────────────────────────────────

            [
                'title'       => 'Average of Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the average (mean) of all numbers in an array.',
                'solution_code' => <<<'JS'
function average(arr) {
    if (arr.length === 0) return 0;
    return arr.reduce((sum, n) => sum + n, 0) / arr.length;
}

console.log(average([1, 2, 3, 4, 5]));   // 3
console.log(average([10, 20, 30]));       // 20
console.log(average([]));                 // 0
JS,
            ],
            [
                'title'       => 'Median of Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the median of an array of numbers.',
                'solution_code' => <<<'JS'
function median(arr) {
    const sorted = [...arr].sort((a, b) => a - b);
    const mid = Math.floor(sorted.length / 2);
    return sorted.length % 2 === 0
        ? (sorted[mid - 1] + sorted[mid]) / 2
        : sorted[mid];
}

console.log(median([3, 1, 2]));         // 2
console.log(median([3, 1, 4, 2]));      // 2.5
console.log(median([7, 2, 10, 9, 4]));  // 7
JS,
            ],
            [
                'title'       => 'Sum of Even Numbers in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the sum of all even numbers in an array.',
                'solution_code' => <<<'JS'
function sumEvens(arr) {
    return arr.filter(n => n % 2 === 0).reduce((sum, n) => sum + n, 0);
}

console.log(sumEvens([1, 2, 3, 4, 5, 6]));   // 12
console.log(sumEvens([1, 3, 5]));              // 0
console.log(sumEvens([2, 4, 6, 8]));           // 20
JS,
            ],
            [
                'title'       => 'Sum of Odd Numbers in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the sum of all odd numbers in an array.',
                'solution_code' => <<<'JS'
function sumOdds(arr) {
    return arr.filter(n => n % 2 !== 0).reduce((sum, n) => sum + n, 0);
}

console.log(sumOdds([1, 2, 3, 4, 5, 6]));   // 9
console.log(sumOdds([2, 4, 6]));              // 0
console.log(sumOdds([1, 3, 5, 7]));           // 16
JS,
            ],
            [
                'title'       => 'Second Largest in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the second largest unique value in an array.',
                'solution_code' => <<<'JS'
function secondLargest(arr) {
    const unique = [...new Set(arr)].sort((a, b) => b - a);
    return unique.length >= 2 ? unique[1] : null;
}

console.log(secondLargest([3, 1, 4, 1, 5, 9, 2]));   // 5
console.log(secondLargest([1, 1, 1]));                 // null
console.log(secondLargest([10, 5, 8]));                // 8
JS,
            ],
            [
                'title'       => 'Second Smallest in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the second smallest unique value in an array.',
                'solution_code' => <<<'JS'
function secondSmallest(arr) {
    const unique = [...new Set(arr)].sort((a, b) => a - b);
    return unique.length >= 2 ? unique[1] : null;
}

console.log(secondSmallest([3, 1, 4, 1, 5, 9, 2]));   // 2
console.log(secondSmallest([5, 5, 5]));                 // null
console.log(secondSmallest([10, 5, 8]));                // 8
JS,
            ],
            [
                'title'       => 'Check if Array is Sorted',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if an array is sorted in ascending order.',
                'solution_code' => <<<'JS'
function isSorted(arr) {
    for (let i = 1; i < arr.length; i++) {
        if (arr[i] < arr[i - 1]) return false;
    }
    return true;
}

console.log(isSorted([1, 2, 3, 4, 5]));    // true
console.log(isSorted([1, 3, 2, 4]));        // false
console.log(isSorted([5, 5, 6]));           // true
JS,
            ],
            [
                'title'       => 'Remove Falsy Values',
                'difficulty'  => 'easy',
                'description' => 'Write a function that removes all falsy values (false, null, 0, "", undefined, NaN) from an array.',
                'solution_code' => <<<'JS'
function compact(arr) {
    return arr.filter(Boolean);
}

console.log(compact([0, 1, false, 2, "", 3, null, undefined, NaN]));
// [1, 2, 3]
console.log(compact([false, null, 0]));   // []
console.log(compact([1, 2, 3]));          // [1, 2, 3]
JS,
            ],
            [
                'title'       => 'Zip Two Arrays',
                'difficulty'  => 'easy',
                'description' => 'Write a function that combines two arrays element-by-element into an array of pairs.',
                'solution_code' => <<<'JS'
function zip(arr1, arr2) {
    const len = Math.min(arr1.length, arr2.length);
    return Array.from({ length: len }, (_, i) => [arr1[i], arr2[i]]);
}

console.log(zip([1, 2, 3], ["a", "b", "c"]));   // [[1,"a"],[2,"b"],[3,"c"]]
console.log(zip([1, 2], [3, 4, 5]));             // [[1,3],[2,4]]
JS,
            ],
            [
                'title'       => 'Array Symmetric Difference',
                'difficulty'  => 'easy',
                'description' => 'Return elements that are in either array but not in both (symmetric difference).',
                'solution_code' => <<<'JS'
function symmetricDifference(arr1, arr2) {
    const set1 = new Set(arr1);
    const set2 = new Set(arr2);
    return [
        ...arr1.filter(x => !set2.has(x)),
        ...arr2.filter(x => !set1.has(x)),
    ];
}

console.log(symmetricDifference([1,2,3],[2,3,4]));   // [1, 4]
console.log(symmetricDifference([1,2],[3,4]));        // [1, 2, 3, 4]
JS,
            ],
            [
                'title'       => 'Array Difference (Subtract)',
                'difficulty'  => 'easy',
                'description' => 'Return elements that are in the first array but not in the second.',
                'solution_code' => <<<'JS'
function difference(arr1, arr2) {
    const set2 = new Set(arr2);
    return arr1.filter(x => !set2.has(x));
}

console.log(difference([1, 2, 3, 4], [2, 4]));     // [1, 3]
console.log(difference([1, 2, 3], [4, 5]));         // [1, 2, 3]
console.log(difference([1, 2], [1, 2, 3]));         // []
JS,
            ],
            [
                'title'       => 'Swap First and Last Elements',
                'difficulty'  => 'easy',
                'description' => 'Write a function that swaps the first and last elements of an array.',
                'solution_code' => <<<'JS'
function swapEnds(arr) {
    if (arr.length < 2) return [...arr];
    const result = [...arr];
    [result[0], result[result.length - 1]] = [result[result.length - 1], result[0]];
    return result;
}

console.log(swapEnds([1, 2, 3, 4, 5]));   // [5, 2, 3, 4, 1]
console.log(swapEnds(["a", "b", "c"]));   // ["c", "b", "a"]
JS,
            ],
            [
                'title'       => 'Double Every Element',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a new array with every element doubled.',
                'solution_code' => <<<'JS'
function doubleAll(arr) {
    return arr.map(n => n * 2);
}

console.log(doubleAll([1, 2, 3, 4]));    // [2, 4, 6, 8]
console.log(doubleAll([-1, 0, 5]));      // [-2, 0, 10]
JS,
            ],
            [
                'title'       => 'Remove Element at Index',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a new array with the element at a given index removed.',
                'solution_code' => <<<'JS'
function removeAt(arr, index) {
    return arr.filter((_, i) => i !== index);
}

console.log(removeAt([1, 2, 3, 4], 1));      // [1, 3, 4]
console.log(removeAt(["a", "b", "c"], 0));   // ["b", "c"]
JS,
            ],
            [
                'title'       => 'Insert Element at Index',
                'difficulty'  => 'easy',
                'description' => 'Write a function that inserts a value into an array at a given index.',
                'solution_code' => <<<'JS'
function insertAt(arr, index, value) {
    return [...arr.slice(0, index), value, ...arr.slice(index)];
}

console.log(insertAt([1, 2, 3], 1, 99));    // [1, 99, 2, 3]
console.log(insertAt([1, 2, 3], 0, 0));     // [0, 1, 2, 3]
console.log(insertAt([1, 2, 3], 3, 4));     // [1, 2, 3, 4]
JS,
            ],
            [
                'title'       => 'Take N Elements from Start',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the first n elements of an array.',
                'solution_code' => <<<'JS'
function take(arr, n) {
    return arr.slice(0, n);
}

console.log(take([1, 2, 3, 4, 5], 3));   // [1, 2, 3]
console.log(take([1, 2], 5));             // [1, 2]
console.log(take([], 3));                 // []
JS,
            ],
            [
                'title'       => 'Take N Elements from End',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the last n elements of an array.',
                'solution_code' => <<<'JS'
function takeLast(arr, n) {
    return arr.slice(-n);
}

console.log(takeLast([1, 2, 3, 4, 5], 3));   // [3, 4, 5]
console.log(takeLast([1, 2], 5));             // [1, 2]
console.log(takeLast([], 3));                 // []
JS,
            ],
            [
                'title'       => 'Maximum Difference in Array',
                'difficulty'  => 'easy',
                'description' => 'Find the maximum difference between any two elements in an array (max - min).',
                'solution_code' => <<<'JS'
function maxDiff(arr) {
    return Math.max(...arr) - Math.min(...arr);
}

console.log(maxDiff([1, 5, 2, 9, 3]));   // 8
console.log(maxDiff([4, 4, 4]));          // 0
console.log(maxDiff([10, 1]));            // 9
JS,
            ],
            [
                'title'       => 'Index of Maximum Value',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the index of the maximum value in an array.',
                'solution_code' => <<<'JS'
function indexOfMax(arr) {
    return arr.indexOf(Math.max(...arr));
}

console.log(indexOfMax([3, 1, 9, 2, 7]));   // 2
console.log(indexOfMax([5, 5, 5]));          // 0
JS,
            ],
            [
                'title'       => 'Index of Minimum Value',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the index of the minimum value in an array.',
                'solution_code' => <<<'JS'
function indexOfMin(arr) {
    return arr.indexOf(Math.min(...arr));
}

console.log(indexOfMin([3, 1, 9, 2, 7]));   // 1
console.log(indexOfMin([5, 5, 5]));          // 0
JS,
            ],
            [
                'title'       => 'Array Contains All Values',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if an array contains every value in a second array.',
                'solution_code' => <<<'JS'
function containsAll(arr, values) {
    const set = new Set(arr);
    return values.every(v => set.has(v));
}

console.log(containsAll([1, 2, 3, 4, 5], [2, 4]));   // true
console.log(containsAll([1, 2, 3], [2, 6]));           // false
console.log(containsAll(["a", "b", "c"], ["a", "b"])); // true
JS,
            ],
            [
                'title'       => 'First Duplicate in Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the first element that appears more than once in an array, or null if there are no duplicates.',
                'solution_code' => <<<'JS'
function firstDuplicate(arr) {
    const seen = new Set();
    for (const item of arr) {
        if (seen.has(item)) return item;
        seen.add(item);
    }
    return null;
}

console.log(firstDuplicate([1, 2, 3, 2, 5]));   // 2
console.log(firstDuplicate([1, 2, 3, 4]));       // null
console.log(firstDuplicate([3, 3, 1, 2]));       // 3
JS,
            ],
            [
                'title'       => 'All Unique Elements Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if all elements in an array are unique.',
                'solution_code' => <<<'JS'
function allUnique(arr) {
    return arr.length === new Set(arr).size;
}

console.log(allUnique([1, 2, 3, 4]));    // true
console.log(allUnique([1, 2, 2, 3]));    // false
console.log(allUnique([]));              // true
JS,
            ],
            [
                'title'       => 'Count Truthy Values',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts the number of truthy values in an array.',
                'solution_code' => <<<'JS'
function countTruthy(arr) {
    return arr.filter(Boolean).length;
}

console.log(countTruthy([0, 1, false, 2, null, 3]));   // 3
console.log(countTruthy([false, null, undefined]));      // 0
console.log(countTruthy([1, 2, 3]));                    // 3
JS,
            ],
            [
                'title'       => 'Flatten Deeply Nested Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that completely flattens a deeply nested array.',
                'solution_code' => <<<'JS'
function flattenDeep(arr) {
    return arr.flat(Infinity);
}

console.log(flattenDeep([1, [2, [3, [4, [5]]]]]));     // [1, 2, 3, 4, 5]
console.log(flattenDeep([[1, 2], [3, [4, 5]]]));        // [1, 2, 3, 4, 5]
JS,
            ],
            [
                'title'       => 'Truncate Array to N Elements',
                'difficulty'  => 'easy',
                'description' => 'Write a function that limits an array to at most n elements from the start.',
                'solution_code' => <<<'JS'
function truncateArray(arr, n) {
    return arr.slice(0, n);
}

console.log(truncateArray([1, 2, 3, 4, 5], 3));   // [1, 2, 3]
console.log(truncateArray([1, 2], 10));            // [1, 2]
JS,
            ],
            [
                'title'       => 'Array of Even Numbers Up to N',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns an array of all even numbers from 0 to n (inclusive).',
                'solution_code' => <<<'JS'
function evensUpTo(n) {
    const result = [];
    for (let i = 0; i <= n; i += 2) result.push(i);
    return result;
}

console.log(evensUpTo(10));    // [0, 2, 4, 6, 8, 10]
console.log(evensUpTo(7));     // [0, 2, 4, 6]
JS,
            ],
            [
                'title'       => 'Array of Odd Numbers Up to N',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns an array of all odd numbers from 1 to n (inclusive).',
                'solution_code' => <<<'JS'
function oddsUpTo(n) {
    const result = [];
    for (let i = 1; i <= n; i += 2) result.push(i);
    return result;
}

console.log(oddsUpTo(10));    // [1, 3, 5, 7, 9]
console.log(oddsUpTo(7));     // [1, 3, 5, 7]
JS,
            ],
            [
                'title'       => 'Array Range',
                'difficulty'  => 'easy',
                'description' => 'Write a function that creates an array of numbers from start to end (inclusive) with an optional step.',
                'solution_code' => <<<'JS'
function range(start, end, step = 1) {
    const result = [];
    for (let i = start; i <= end; i += step) result.push(i);
    return result;
}

console.log(range(1, 5));        // [1, 2, 3, 4, 5]
console.log(range(0, 10, 2));    // [0, 2, 4, 6, 8, 10]
console.log(range(5, 5));        // [5]
JS,
            ],
            [
                'title'       => 'Check if Two Arrays Are Equal',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if two arrays have the same elements in the same order.',
                'solution_code' => <<<'JS'
function arraysEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) return false;
    return arr1.every((val, i) => val === arr2[i]);
}

console.log(arraysEqual([1, 2, 3], [1, 2, 3]));    // true
console.log(arraysEqual([1, 2, 3], [1, 2, 4]));    // false
console.log(arraysEqual([1, 2], [1, 2, 3]));        // false
JS,
            ],
            [
                'title'       => 'Sum of Nested Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that sums all numbers in a deeply nested array.',
                'solution_code' => <<<'JS'
function sumNested(arr) {
    return arr.flat(Infinity).reduce((sum, n) => sum + n, 0);
}

console.log(sumNested([1, [2, [3, [4]]]]]);     // 10
console.log(sumNested([[1, 2], [3, [4, 5]]]));  // 15
console.log(sumNested([10]));                   // 10
JS,
            ],
            [
                'title'       => 'Unique Values by Property',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns unique objects from an array based on a specific property.',
                'solution_code' => <<<'JS'
function uniqueBy(arr, key) {
    const seen = new Set();
    return arr.filter(item => {
        const val = item[key];
        if (seen.has(val)) return false;
        seen.add(val);
        return true;
    });
}

const data = [
    { id: 1, name: "Alice" },
    { id: 2, name: "Bob" },
    { id: 1, name: "Alice Duplicate" },
];
console.log(JSON.stringify(uniqueBy(data, "id")));
// [{ id:1, name:"Alice" }, { id:2, name:"Bob" }]
JS,
            ],
            [
                'title'       => 'Running Total (Prefix Sum)',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a new array where each element is the cumulative sum up to that index.',
                'solution_code' => <<<'JS'
function runningTotal(arr) {
    let sum = 0;
    return arr.map(n => (sum += n));
}

console.log(runningTotal([1, 2, 3, 4, 5]));   // [1, 3, 6, 10, 15]
console.log(runningTotal([10, -3, 5]));        // [10, 7, 12]
JS,
            ],

            // ─── OBJECTS (from EasyProblemSeeder) ────────────────────────────────────

            [
                'title'       => 'Count Keys in Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the number of own enumerable keys in an object.',
                'solution_code' => <<<'JS'
function countKeys(obj) {
    return Object.keys(obj).length;
}

console.log(countKeys({ a: 1, b: 2, c: 3 }));   // 3
console.log(countKeys({}));                       // 0
console.log(countKeys({ x: 10 }));               // 1
JS,
            ],
            [
                'title'       => 'Check if Object Has Key',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if an object has a given key as its own property.',
                'solution_code' => <<<'JS'
function hasKey(obj, key) {
    return Object.prototype.hasOwnProperty.call(obj, key);
}

console.log(hasKey({ a: 1, b: 2 }, "a"));   // true
console.log(hasKey({ a: 1, b: 2 }, "c"));   // false
JS,
            ],
            [
                'title'       => 'Invert Object (Swap Keys and Values)',
                'difficulty'  => 'easy',
                'description' => 'Write a function that swaps all keys and values of an object.',
                'solution_code' => <<<'JS'
function invertObject(obj) {
    return Object.fromEntries(Object.entries(obj).map(([k, v]) => [v, k]));
}

console.log(invertObject({ a: 1, b: 2, c: 3 }));   // { 1: "a", 2: "b", 3: "c" }
console.log(invertObject({ one: "1", two: "2" }));  // { "1": "one", "2": "two" }
JS,
            ],
            [
                'title'       => 'Pick Keys from Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that creates a new object containing only the specified keys from a source object.',
                'solution_code' => <<<'JS'
function pick(obj, keys) {
    return keys.reduce((result, key) => {
        if (Object.prototype.hasOwnProperty.call(obj, key)) result[key] = obj[key];
        return result;
    }, {});
}

console.log(pick({ a: 1, b: 2, c: 3 }, ["a", "c"]));   // { a: 1, c: 3 }
console.log(pick({ x: 10, y: 20 }, ["y", "z"]));        // { y: 20 }
JS,
            ],
            [
                'title'       => 'Omit Keys from Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that creates a new object excluding the specified keys.',
                'solution_code' => <<<'JS'
function omit(obj, keys) {
    const excluded = new Set(keys);
    return Object.fromEntries(
        Object.entries(obj).filter(([k]) => !excluded.has(k))
    );
}

console.log(omit({ a: 1, b: 2, c: 3 }, ["b"]));      // { a: 1, c: 3 }
console.log(omit({ x: 10, y: 20, z: 30 }, ["x","z"])); // { y: 20 }
JS,
            ],
            [
                'title'       => 'Convert Array of Pairs to Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that converts an array of [key, value] pairs into an object.',
                'solution_code' => <<<'JS'
function pairsToObject(pairs) {
    return Object.fromEntries(pairs);
}

console.log(pairsToObject([["a", 1], ["b", 2], ["c", 3]]));
// { a: 1, b: 2, c: 3 }
JS,
            ],
            [
                'title'       => 'Sum of Object Values',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the sum of all numeric values in an object.',
                'solution_code' => <<<'JS'
function sumValues(obj) {
    return Object.values(obj).reduce((sum, v) => sum + v, 0);
}

console.log(sumValues({ a: 1, b: 2, c: 3 }));    // 6
console.log(sumValues({ x: 10, y: -5, z: 0 })); // 5
JS,
            ],
            [
                'title'       => 'Find Object in Array by Property',
                'difficulty'  => 'easy',
                'description' => 'Write a function that finds and returns the first object in an array that has a matching property value.',
                'solution_code' => <<<'JS'
function findByProp(arr, key, value) {
    return arr.find(item => item[key] === value) || null;
}

const users = [
    { id: 1, name: "Alice" },
    { id: 2, name: "Bob" },
    { id: 3, name: "Charlie" },
];

console.log(findByProp(users, "name", "Bob"));   // { id: 2, name: "Bob" }
console.log(findByProp(users, "id", 99));        // null
JS,
            ],
            [
                'title'       => 'Group Array by Key',
                'difficulty'  => 'easy',
                'description' => 'Write a function that groups an array of objects by a given key.',
                'solution_code' => <<<'JS'
function groupBy(arr, key) {
    return arr.reduce((groups, item) => {
        const groupKey = item[key];
        if (!groups[groupKey]) groups[groupKey] = [];
        groups[groupKey].push(item);
        return groups;
    }, {});
}

const people = [
    { name: "Alice", dept: "Engineering" },
    { name: "Bob", dept: "Marketing" },
    { name: "Carol", dept: "Engineering" },
];
console.log(JSON.stringify(groupBy(people, "dept")));
// { Engineering: [{...Alice}, {...Carol}], Marketing: [{...Bob}] }
JS,
            ],
            [
                'title'       => 'Convert Object Array to Lookup Map',
                'difficulty'  => 'easy',
                'description' => 'Convert an array of objects into a lookup object keyed by a unique property.',
                'solution_code' => <<<'JS'
function toLookup(arr, keyProp) {
    return arr.reduce((map, item) => {
        map[item[keyProp]] = item;
        return map;
    }, {});
}

const items = [{ id: 1, name: "A" }, { id: 2, name: "B" }];
const map = toLookup(items, "id");
console.log(map[1]);   // { id: 1, name: "A" }
console.log(map[2]);   // { id: 2, name: "B" }
JS,
            ],
            [
                'title'       => 'Count by Property',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts how many objects in an array have each distinct value for a given key.',
                'solution_code' => <<<'JS'
function countBy(arr, key) {
    return arr.reduce((counts, item) => {
        counts[item[key]] = (counts[item[key]] || 0) + 1;
        return counts;
    }, {});
}

const products = [
    { name: "A", category: "fruit" },
    { name: "B", category: "veg" },
    { name: "C", category: "fruit" },
];
console.log(countBy(products, "category"));   // { fruit: 2, veg: 1 }
JS,
            ],
            [
                'title'       => 'Merge Array of Objects',
                'difficulty'  => 'easy',
                'description' => "Write a function that merges an array of objects into a single object. Later objects' properties override earlier ones on key conflict.",
                'solution_code' => <<<'JS'
function mergeAll(arr) {
    return Object.assign({}, ...arr);
}

console.log(mergeAll([{ a: 1 }, { b: 2 }, { c: 3 }]));     // { a: 1, b: 2, c: 3 }
console.log(mergeAll([{ a: 1, b: 2 }, { b: 99, c: 3 }]));  // { a: 1, b: 99, c: 3 }
JS,
            ],
            [
                'title'       => 'Default Values for Object Properties',
                'difficulty'  => 'easy',
                'description' => 'Write a function that fills missing properties in an object with default values.',
                'solution_code' => <<<'JS'
function defaults(obj, defaultValues) {
    return { ...defaultValues, ...obj };
}

const config = { timeout: 3000 };
console.log(defaults(config, { timeout: 5000, retries: 3, debug: false }));
// { timeout: 3000, retries: 3, debug: false }
JS,
            ],
            [
                'title'       => 'Flatten Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that flattens a nested object into a single level using dot-notation keys.',
                'solution_code' => <<<'JS'
function flattenObject(obj, prefix = "", result = {}) {
    for (const key in obj) {
        const fullKey = prefix ? `${prefix}.${key}` : key;
        if (typeof obj[key] === "object" && obj[key] !== null && !Array.isArray(obj[key])) {
            flattenObject(obj[key], fullKey, result);
        } else {
            result[fullKey] = obj[key];
        }
    }
    return result;
}

console.log(flattenObject({ a: 1, b: { c: 2, d: { e: 3 } } }));
// { a: 1, "b.c": 2, "b.d.e": 3 }
JS,
            ],

            // ─── BASICS / UTILITY (from EasyProblemSeeder) ───────────────────────────

            [
                'title'       => 'Generate Random Integer in Range',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a random integer between min and max (inclusive).',
                'solution_code' => <<<'JS'
function randomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Results will vary since they are random:
const r1 = randomInt(1, 10);
console.log(r1 >= 1 && r1 <= 10);    // true

const r2 = randomInt(50, 100);
console.log(r2 >= 50 && r2 <= 100);  // true
JS,
            ],
            [
                'title'       => 'Check if Value is Array',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a value is an Array.',
                'solution_code' => <<<'JS'
function isArray(value) {
    return Array.isArray(value);
}

console.log(isArray([1, 2, 3]));     // true
console.log(isArray("string"));      // false
console.log(isArray({ a: 1 }));      // false
console.log(isArray(null));          // false
JS,
            ],
            [
                'title'       => 'Check if Value is Plain Object',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a value is a plain object (not null, not an array, not a function).',
                'solution_code' => <<<'JS'
function isPlainObject(value) {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

console.log(isPlainObject({ a: 1 }));   // true
console.log(isPlainObject([1, 2]));     // false
console.log(isPlainObject(null));       // false
console.log(isPlainObject("hello"));    // false
JS,
            ],
            [
                'title'       => 'Type Checker Function',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a human-readable type name for any value: "null", "array", "object", or the result of typeof.',
                'solution_code' => <<<'JS'
function typeOf(value) {
    if (value === null) return "null";
    if (Array.isArray(value)) return "array";
    return typeof value;
}

console.log(typeOf(42));          // "number"
console.log(typeOf("hi"));        // "string"
console.log(typeOf(null));        // "null"
console.log(typeOf([1, 2]));      // "array"
console.log(typeOf({ a: 1 }));    // "object"
console.log(typeOf(true));        // "boolean"
JS,
            ],
            [
                'title'       => 'Safe JSON Parse',
                'difficulty'  => 'easy',
                'description' => 'Write a function that parses a JSON string without throwing. Returns a fallback value if parsing fails.',
                'solution_code' => <<<'JS'
function safeJsonParse(str, fallback = null) {
    try {
        return JSON.parse(str);
    } catch {
        return fallback;
    }
}

console.log(safeJsonParse('{"a":1}'));      // { a: 1 }
console.log(safeJsonParse("[1,2,3]"));      // [1, 2, 3]
console.log(safeJsonParse("bad json"));     // null
console.log(safeJsonParse("oops", []));     // []
JS,
            ],
            [
                'title'       => 'Is Integer Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a value is an integer (whole number, not a float).',
                'solution_code' => <<<'JS'
function isInteger(n) {
    return Number.isInteger(n);
}

console.log(isInteger(4));      // true
console.log(isInteger(-2));     // true
console.log(isInteger(4.5));    // false
console.log(isInteger("4"));    // false
console.log(isInteger(NaN));    // false
JS,
            ],
            [
                'title'       => 'Number Abbreviation',
                'difficulty'  => 'easy',
                'description' => 'Write a function that abbreviates large numbers: 1000 → "1K", 1000000 → "1M".',
                'solution_code' => <<<'JS'
function abbreviateNumber(n) {
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, "") + "M";
    if (n >= 1_000) return (n / 1_000).toFixed(1).replace(/\.0$/, "") + "K";
    return String(n);
}

console.log(abbreviateNumber(999));        // "999"
console.log(abbreviateNumber(1000));       // "1K"
console.log(abbreviateNumber(1500));       // "1.5K"
console.log(abbreviateNumber(2000000));    // "2M"
JS,
            ],
            [
                'title'       => 'Deep Equality Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that checks if two values are deeply equal (primitives, arrays, and plain objects).',
                'solution_code' => <<<'JS'
function deepEqual(a, b) {
    if (a === b) return true;
    if (typeof a !== typeof b) return false;
    if (Array.isArray(a) && Array.isArray(b)) {
        if (a.length !== b.length) return false;
        return a.every((v, i) => deepEqual(v, b[i]));
    }
    if (typeof a === "object" && a !== null && b !== null) {
        const keysA = Object.keys(a), keysB = Object.keys(b);
        if (keysA.length !== keysB.length) return false;
        return keysA.every(k => deepEqual(a[k], b[k]));
    }
    return false;
}

console.log(deepEqual([1, [2, 3]], [1, [2, 3]]));           // true
console.log(deepEqual({ a: 1, b: [2] }, { a: 1, b: [2] })); // true
console.log(deepEqual({ a: 1 }, { a: 2 }));                  // false
JS,
            ],
            [
                'title'       => 'Check Divisibility',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a number is divisible by all values in a given array.',
                'solution_code' => <<<'JS'
function isDivisibleBy(n, divisors) {
    return divisors.every(d => n % d === 0);
}

console.log(isDivisibleBy(12, [2, 3, 4]));   // true
console.log(isDivisibleBy(10, [2, 3]));       // false
console.log(isDivisibleBy(30, [2, 3, 5]));   // true
JS,
            ],
            [
                'title'       => 'Promise-Based Sleep',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns a Promise that resolves after a given number of milliseconds.',
                'solution_code' => <<<'JS'
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function demo() {
    console.log("Start");
    await sleep(100);
    console.log("After 100ms");
    await sleep(200);
    console.log("After another 200ms");
}

demo();
JS,
            ],
            [
                'title'       => 'Memoize a Function',
                'difficulty'  => 'easy',
                'description' => 'Implement a memoize function that caches the results of function calls by their arguments.',
                'solution_code' => <<<'JS'
function memoize(fn) {
    const cache = new Map();
    return function(...args) {
        const key = JSON.stringify(args);
        if (cache.has(key)) return cache.get(key);
        const result = fn.apply(this, args);
        cache.set(key, result);
        return result;
    };
}

let callCount = 0;
const expensiveAdd = memoize((a, b) => {
    callCount++;
    return a + b;
});

console.log(expensiveAdd(2, 3));    // 5
console.log(expensiveAdd(2, 3));    // 5 (cached)
console.log(callCount);             // 1 (only computed once)
JS,
            ],
            [
                'title'       => 'Pipe Functions',
                'difficulty'  => 'easy',
                'description' => 'Write a pipe function that takes a series of functions and returns a new function that passes its input through each function left to right.',
                'solution_code' => <<<'JS'
function pipe(...fns) {
    return (value) => fns.reduce((acc, fn) => fn(acc), value);
}

const double = x => x * 2;
const addTen = x => x + 10;
const square = x => x * x;

const transform = pipe(double, addTen, square);
console.log(transform(5));    // ((5*2)+10)^2 = 400
console.log(transform(1));    // ((1*2)+10)^2 = 144
JS,
            ],
            [
                'title'       => 'Compose Functions',
                'difficulty'  => 'easy',
                'description' => 'Write a compose function that combines functions right to left (the opposite of pipe).',
                'solution_code' => <<<'JS'
function compose(...fns) {
    return (value) => fns.reduceRight((acc, fn) => fn(acc), value);
}

const trim   = s => s.trim();
const lower  = s => s.toLowerCase();
const exclaim = s => s + "!";

const format = compose(exclaim, lower, trim);
console.log(format("  Hello World  "));   // "hello world!"
JS,
            ],
            [
                'title'       => 'Curry a Function',
                'difficulty'  => 'easy',
                'description' => 'Implement a curry function that transforms a binary function (a, b) into a curried version f(a)(b).',
                'solution_code' => <<<'JS'
function curry(fn) {
    return function curried(...args) {
        if (args.length >= fn.length) return fn(...args);
        return (...moreArgs) => curried(...args, ...moreArgs);
    };
}

const add = curry((a, b) => a + b);
console.log(add(3)(4));       // 7
console.log(add(10)(5));      // 15

const multiply = curry((a, b, c) => a * b * c);
console.log(multiply(2)(3)(4));   // 24
JS,
            ],
            [
                'title'       => 'Once Function',
                'difficulty'  => 'easy',
                'description' => 'Write a once() higher-order function that ensures a function can only be called once. Subsequent calls return the first result.',
                'solution_code' => <<<'JS'
function once(fn) {
    let called = false;
    let result;
    return function(...args) {
        if (!called) {
            called = true;
            result = fn.apply(this, args);
        }
        return result;
    };
}

const init = once(() => {
    console.log("Initializing…");
    return 42;
});

console.log(init());   // "Initializing…" then 42
console.log(init());   // 42 (no log, same result)
console.log(init());   // 42
JS,
            ],
            [
                'title'       => 'Partial Application',
                'difficulty'  => 'easy',
                'description' => 'Implement partial application: return a new function with some arguments pre-filled.',
                'solution_code' => <<<'JS'
function partial(fn, ...presetArgs) {
    return function(...laterArgs) {
        return fn(...presetArgs, ...laterArgs);
    };
}

function greet(greeting, name) {
    return `${greeting}, ${name}!`;
}

const sayHello = partial(greet, "Hello");
const sayHi    = partial(greet, "Hi");

console.log(sayHello("Alice"));   // "Hello, Alice!"
console.log(sayHi("Bob"));        // "Hi, Bob!"
JS,
            ],
        ];
    }
}
