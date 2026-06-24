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

        $this->command->info('Seeded ' . count($this->problems()) . ' Math problems (201–243).');
    }

    private function problems(): array
    {
        return [

            // ─── FROM PROBLEMSEEDER (MATH/EASY) ─────────────────────────────────────

            [
                'title'       => 'Sum of Two Numbers',
                'difficulty'  => 'easy',
                'description' => 'Write a function that takes two numbers and returns their sum.',
                'solution_code' => <<<'JS'
function sum(a, b) {
    return a + b;
}

console.log(sum(3, 7));    // 10
console.log(sum(-1, 5));   // 4
console.log(sum(0, 0));    // 0
JS,
            ],
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
                'description' => 'Find the LCM of two positive integers. Use: LCM(a, b) = (a × b) / GCD(a, b).',
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

console.log(lcm(4, 6));                    // 12
console.log(lcm(12, 18));                  // 36
console.log(lcmArray([2, 3, 4, 5]));       // 60
JS,
            ],
            [
                'title'       => 'Power of Two Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a number is a power of two using the bit trick: n & (n-1) === 0.',
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
                'title'       => 'Number to Binary',
                'difficulty'  => 'easy',
                'description' => 'Write a function that converts a non-negative integer to its binary string representation.',
                'solution_code' => <<<'JS'
function toBinary(n) {
    return (n >>> 0).toString(2);
}

console.log(toBinary(5));     // "101"
console.log(toBinary(10));    // "1010"
console.log(toBinary(255));   // "11111111"
console.log(toBinary(0));     // "0"
JS,
            ],
            [
                'title'       => 'Celsius to Fahrenheit',
                'difficulty'  => 'easy',
                'description' => 'Write a function that converts a temperature from Celsius to Fahrenheit using F = (C × 9/5) + 32.',
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

            // ─── MATH FUNDAMENTALS ───────────────────────────────────────────────────

            [
                'title'       => 'Digital Root',
                'difficulty'  => 'easy',
                'description' => 'The digital root is the single digit obtained by repeatedly summing the digits until one digit remains. Also has the O(1) formula: 1 + (n−1) % 9 for n > 0.',
                'solution_code' => <<<'JS'
// Iterative
function digitalRoot(n) {
    while (n >= 10) {
        n = String(n).split("").reduce((s, d) => s + +d, 0);
    }
    return n;
}

// O(1) formula
function digitalRootFast(n) {
    if (n === 0) return 0;
    return 1 + (n - 1) % 9;
}

console.log(digitalRoot(493));         // 7
console.log(digitalRoot(942));         // 6
console.log(digitalRootFast(493));     // 7
console.log(digitalRootFast(9));       // 9
JS,
            ],
            [
                'title'       => 'Count Digits in Number',
                'difficulty'  => 'easy',
                'description' => 'Write a function that counts the number of digits in an integer.',
                'solution_code' => <<<'JS'
function countDigits(n) {
    return String(Math.abs(n)).length;
}

console.log(countDigits(0));       // 1
console.log(countDigits(123));     // 3
console.log(countDigits(-9876));   // 4
console.log(countDigits(1000));    // 4
JS,
            ],
            [
                'title'       => 'Reverse Digits',
                'difficulty'  => 'easy',
                'description' => 'Write a function that reverses the digits of an integer (preserving sign).',
                'solution_code' => <<<'JS'
function reverseDigits(n) {
    const sign = n < 0 ? -1 : 1;
    const reversed = String(Math.abs(n)).split("").reverse().join("");
    return sign * Number(reversed);
}

console.log(reverseDigits(1234));    // 4321
console.log(reverseDigits(-567));    // -765
console.log(reverseDigits(100));     // 1
console.log(reverseDigits(0));       // 0
JS,
            ],
            [
                'title'       => 'Perfect Square Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a number is a perfect square (without using Math.sqrt).',
                'solution_code' => <<<'JS'
function isPerfectSquare(n) {
    if (n < 0) return false;
    const root = Math.round(Math.sqrt(n));
    return root * root === n;
}

// Without Math.sqrt — binary search approach:
function isPerfectSquareBinary(n) {
    if (n < 0) return false;
    let lo = 0, hi = n;
    while (lo <= hi) {
        const mid = Math.floor((lo + hi) / 2);
        const sq = mid * mid;
        if (sq === n) return true;
        if (sq < n) lo = mid + 1;
        else hi = mid - 1;
    }
    return false;
}

console.log(isPerfectSquare(16));    // true
console.log(isPerfectSquare(14));    // false
console.log(isPerfectSquareBinary(25));   // true
console.log(isPerfectSquareBinary(26));   // false
JS,
            ],
            [
                'title'       => 'Count Divisors',
                'difficulty'  => 'easy',
                'description' => 'Return the total number of divisors of a positive integer n. Iterate up to √n and count both i and n/i.',
                'solution_code' => <<<'JS'
function countDivisors(n) {
    let count = 0;
    for (let i = 1; i <= Math.sqrt(n); i++) {
        if (n % i === 0) {
            count += i === n / i ? 1 : 2;
        }
    }
    return count;
}

function listDivisors(n) {
    const divs = [];
    for (let i = 1; i <= Math.sqrt(n); i++) {
        if (n % i === 0) {
            divs.push(i);
            if (i !== n / i) divs.push(n / i);
        }
    }
    return divs.sort((a, b) => a - b);
}

console.log(countDivisors(12));      // 6
console.log(countDivisors(36));      // 9 (perfect square)
console.log(listDivisors(12));       // [1, 2, 3, 4, 6, 12]
JS,
            ],
            [
                'title'       => 'Armstrong Number',
                'difficulty'  => 'easy',
                'description' => 'An Armstrong (narcissistic) number equals the sum of its own digits raised to the power of the digit count. E.g. 153 = 1³ + 5³ + 3³.',
                'solution_code' => <<<'JS'
function isArmstrong(n) {
    const digits = String(n).split("");
    const power  = digits.length;
    const sum    = digits.reduce((acc, d) => acc + d ** power, 0);
    return sum === n;
}

console.log(isArmstrong(1));     // true
console.log(isArmstrong(153));   // true  (1³+5³+3³)
console.log(isArmstrong(370));   // true  (3³+7³+0³)
console.log(isArmstrong(9474));  // true  (9⁴+4⁴+7⁴+4⁴)
console.log(isArmstrong(10));    // false
JS,
            ],
            [
                'title'       => 'Perfect Number Check',
                'difficulty'  => 'easy',
                'description' => 'A perfect number equals the sum of its proper divisors (all divisors except itself). E.g. 6 = 1+2+3.',
                'solution_code' => <<<'JS'
function isPerfect(n) {
    if (n <= 1) return false;
    let sum = 1;
    for (let i = 2; i <= Math.sqrt(n); i++) {
        if (n % i === 0) {
            sum += i;
            if (i !== n / i) sum += n / i;
        }
    }
    return sum === n;
}

console.log(isPerfect(6));     // true  (1+2+3)
console.log(isPerfect(28));    // true  (1+2+4+7+14)
console.log(isPerfect(496));   // true
console.log(isPerfect(12));    // false
JS,
            ],
            [
                'title'       => 'Leap Year Check',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns true if a year is a leap year. A leap year is divisible by 4, except for century years, which must be divisible by 400.',
                'solution_code' => <<<'JS'
function isLeapYear(year) {
    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
}

console.log(isLeapYear(2000));   // true  (divisible by 400)
console.log(isLeapYear(1900));   // false (divisible by 100 but not 400)
console.log(isLeapYear(2024));   // true
console.log(isLeapYear(2023));   // false
JS,
            ],
            [
                'title'       => 'Euclidean Distance',
                'difficulty'  => 'easy',
                'description' => 'Write a function that calculates the Euclidean distance between two 2D points.',
                'solution_code' => <<<'JS'
function distance(x1, y1, x2, y2) {
    return Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2);
}

console.log(distance(0, 0, 3, 4));       // 5
console.log(distance(1, 1, 4, 5));       // 5
console.log(+distance(0, 0, 1, 1).toFixed(4));   // 1.4142
JS,
            ],
            [
                'title'       => 'Circle Area and Circumference',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the area (πr²) and circumference (2πr) of a circle given its radius.',
                'solution_code' => <<<'JS'
function circleProps(r) {
    return {
        area:          +(Math.PI * r ** 2).toFixed(4),
        circumference: +(2 * Math.PI * r).toFixed(4),
    };
}

console.log(circleProps(1));    // { area: 3.1416, circumference: 6.2832 }
console.log(circleProps(5));    // { area: 78.5398, circumference: 31.4159 }
console.log(circleProps(0));    // { area: 0, circumference: 0 }
JS,
            ],
            [
                'title'       => 'Hypotenuse Length',
                'difficulty'  => 'easy',
                'description' => 'Given the two legs of a right triangle, return the hypotenuse using the Pythagorean theorem: c = √(a² + b²).',
                'solution_code' => <<<'JS'
function hypotenuse(a, b) {
    return Math.sqrt(a * a + b * b);
}

console.log(hypotenuse(3, 4));    // 5
console.log(hypotenuse(5, 12));   // 13
console.log(+hypotenuse(1, 1).toFixed(4));  // 1.4142
JS,
            ],
            [
                'title'       => 'Clamp Number to Range',
                'difficulty'  => 'easy',
                'description' => 'Write a function that clamps a number to the [min, max] range — returns min if below, max if above, otherwise the number itself.',
                'solution_code' => <<<'JS'
function clamp(n, min, max) {
    return Math.min(Math.max(n, min), max);
}

console.log(clamp(5, 1, 10));    // 5
console.log(clamp(0, 1, 10));    // 1
console.log(clamp(15, 1, 10));   // 10
console.log(clamp(-5, 0, 100));  // 0
JS,
            ],
            [
                'title'       => 'Round to N Decimal Places',
                'difficulty'  => 'easy',
                'description' => 'Write a function that rounds a number to exactly n decimal places.',
                'solution_code' => <<<'JS'
function roundTo(n, decimals) {
    const factor = 10 ** decimals;
    return Math.round(n * factor) / factor;
}

console.log(roundTo(3.14159, 2));    // 3.14
console.log(roundTo(1.005, 2));      // 1.01
console.log(roundTo(1234.5678, 3));  // 1234.568
console.log(roundTo(99.9, 0));       // 100
JS,
            ],
            [
                'title'       => 'Calculate Percentage',
                'difficulty'  => 'easy',
                'description' => 'Write a function that calculates what percentage one number is of another.',
                'solution_code' => <<<'JS'
function percentage(part, total) {
    if (total === 0) return 0;
    return (part / total) * 100;
}

console.log(percentage(25, 100));    // 25
console.log(percentage(1, 3));       // 33.33333...
console.log(+percentage(1, 3).toFixed(2));  // 33.33
console.log(percentage(50, 200));    // 25
JS,
            ],
            [
                'title'       => 'Absolute Difference',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the absolute difference between two numbers.',
                'solution_code' => <<<'JS'
function absDiff(a, b) {
    return Math.abs(a - b);
}

console.log(absDiff(10, 3));    // 7
console.log(absDiff(3, 10));    // 7
console.log(absDiff(-5, 5));    // 10
console.log(absDiff(0, 0));     // 0
JS,
            ],
            [
                'title'       => 'Midpoint Between Two Points',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns the midpoint between two 2D points.',
                'solution_code' => <<<'JS'
function midpoint(x1, y1, x2, y2) {
    return { x: (x1 + x2) / 2, y: (y1 + y2) / 2 };
}

console.log(midpoint(0, 0, 4, 6));    // { x: 2, y: 3 }
console.log(midpoint(-2, -4, 2, 4));  // { x: 0, y: 0 }
console.log(midpoint(1, 1, 3, 3));    // { x: 2, y: 2 }
JS,
            ],
            [
                'title'       => 'Sum of First N Natural Numbers',
                'difficulty'  => 'easy',
                'description' => 'Return the sum 1 + 2 + 3 + … + n using the closed-form formula: n(n+1)/2.',
                'solution_code' => <<<'JS'
function sumN(n) {
    return (n * (n + 1)) / 2;
}

console.log(sumN(1));     // 1
console.log(sumN(5));     // 15   (1+2+3+4+5)
console.log(sumN(10));    // 55
console.log(sumN(100));   // 5050
JS,
            ],
            [
                'title'       => 'Number Sign (Signum)',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns 1 if the number is positive, -1 if negative, and 0 if zero.',
                'solution_code' => <<<'JS'
function signum(n) {
    if (n > 0) return 1;
    if (n < 0) return -1;
    return 0;
}

// Or using Math.sign:
function signumBuiltin(n) {
    return Math.sign(n);
}

console.log(signum(42));     // 1
console.log(signum(-7));     // -1
console.log(signum(0));      // 0
console.log(Math.sign(-0));  // -0
JS,
            ],
            [
                'title'       => 'Seconds to HH:MM:SS',
                'difficulty'  => 'easy',
                'description' => 'Convert a total number of seconds to a HH:MM:SS time string.',
                'solution_code' => <<<'JS'
function toTimeString(totalSeconds) {
    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;
    return [h, m, s]
        .map(unit => String(unit).padStart(2, "0"))
        .join(":");
}

console.log(toTimeString(0));       // "00:00:00"
console.log(toTimeString(61));      // "00:01:01"
console.log(toTimeString(3661));    // "01:01:01"
console.log(toTimeString(86399));   // "23:59:59"
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
                'title'       => 'Multiples of N Up to Limit',
                'difficulty'  => 'easy',
                'description' => 'Write a function that returns an array of all multiples of n up to (and including) a given limit.',
                'solution_code' => <<<'JS'
function multiplesOf(n, limit) {
    const result = [];
    for (let i = n; i <= limit; i += n) result.push(i);
    return result;
}

console.log(multiplesOf(3, 15));    // [3, 6, 9, 12, 15]
console.log(multiplesOf(7, 30));    // [7, 14, 21, 28]
console.log(multiplesOf(5, 4));     // []
JS,
            ],
            [
                'title'       => 'Minutes to Hours and Minutes',
                'difficulty'  => 'easy',
                'description' => 'Convert a number of minutes to an object with hours and remaining minutes.',
                'solution_code' => <<<'JS'
function minutesToTime(totalMinutes) {
    const hours   = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return { hours, minutes };
}

console.log(minutesToTime(90));     // { hours: 1, minutes: 30 }
console.log(minutesToTime(125));    // { hours: 2, minutes: 5 }
console.log(minutesToTime(60));     // { hours: 1, minutes: 0 }
console.log(minutesToTime(45));     // { hours: 0, minutes: 45 }
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
            [
                'title'       => 'Matrix Addition',
                'difficulty'  => 'easy',
                'description' => 'Write a function that adds two matrices of the same dimensions element-by-element.',
                'solution_code' => <<<'JS'
function addMatrices(A, B) {
    return A.map((row, i) => row.map((val, j) => val + B[i][j]));
}

const A = [[1, 2], [3, 4]];
const B = [[5, 6], [7, 8]];
console.log(addMatrices(A, B));    // [[6, 8], [10, 12]]

const C = [[1, 0, 0], [0, 1, 0], [0, 0, 1]];
const D = [[9, 8, 7], [6, 5, 4], [3, 2, 1]];
console.log(addMatrices(C, D));    // [[10, 8, 7], [6, 6, 4], [3, 2, 2]]
JS,
            ],
            [
                'title'       => 'Matrix Transpose',
                'difficulty'  => 'easy',
                'description' => 'Write a function that transposes a matrix (flips rows and columns).',
                'solution_code' => <<<'JS'
function transpose(matrix) {
    if (!matrix.length) return [];
    const rows = matrix.length, cols = matrix[0].length;
    return Array.from({ length: cols }, (_, j) =>
        Array.from({ length: rows }, (_, i) => matrix[i][j])
    );
}

const m = [[1, 2, 3], [4, 5, 6]];
console.log(transpose(m));    // [[1, 4], [2, 5], [3, 6]]

const square = [[1, 2], [3, 4]];
console.log(transpose(square));    // [[1, 3], [2, 4]]
JS,
            ],

            // ─── FROM MATHPROBLEMSEEDER (UNIQUE) ────────────────────────────────────

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
console.log(fastPow(2, 32));    // 4294967296
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
console.log(nCr(52, 5));    // 2598960 (5-card poker hands)
JS,
            ],
            [
                'title'       => 'Standard Deviation',
                'difficulty'  => 'medium',
                'description' => 'Compute the population standard deviation of an array: σ = √( (1/n) × Σ(xi − μ)² ).',
                'solution_code' => <<<'JS'
function standardDeviation(nums) {
    const n    = nums.length;
    const mean = nums.reduce((s, x) => s + x, 0) / n;
    const variance = nums.reduce((s, x) => s + (x - mean) ** 2, 0) / n;
    return Math.sqrt(variance);
}

function sampleStdDev(nums) {
    const n    = nums.length;
    const mean = nums.reduce((s, x) => s + x, 0) / n;
    const variance = nums.reduce((s, x) => s + (x - mean) ** 2, 0) / (n - 1);
    return Math.sqrt(variance);
}

const data = [2, 4, 4, 4, 5, 5, 7, 9];
console.log(+standardDeviation(data).toFixed(4));    // 2
console.log(+sampleStdDev(data).toFixed(4));         // 2.1381
JS,
            ],
            [
                'title'       => 'Collatz Sequence Length',
                'difficulty'  => 'easy',
                'description' => 'Starting from n, repeatedly apply n/2 if even or 3n+1 if odd. Return the number of steps to reach 1.',
                'solution_code' => <<<'JS'
function collatzLength(n) {
    let steps = 0;
    while (n !== 1) {
        n = n % 2 === 0 ? n / 2 : 3 * n + 1;
        steps++;
    }
    return steps;
}

console.log(collatzLength(1));     // 0
console.log(collatzLength(6));     // 8   (6→3→10→5→16→8→4→2→1)
console.log(collatzLength(27));    // 111
console.log(collatzLength(100));   // 25
JS,
            ],
            [
                'title'       => 'Average, Median and Mode',
                'difficulty'  => 'medium',
                'description' => 'Compute the three measures of central tendency for an array of numbers: Mean, Median, and Mode (most frequent values).',
                'solution_code' => <<<'JS'
function statistics(nums) {
    const n = nums.length;
    const mean = nums.reduce((s, x) => s + x, 0) / n;

    const sorted = [...nums].sort((a, b) => a - b);
    const median = n % 2 === 0
        ? (sorted[n / 2 - 1] + sorted[n / 2]) / 2
        : sorted[Math.floor(n / 2)];

    const freq = nums.reduce((map, x) => { map[x] = (map[x] || 0) + 1; return map; }, {});
    const maxFreq = Math.max(...Object.values(freq));
    const mode = Object.keys(freq).filter(k => freq[k] === maxFreq).map(Number);

    return { mean, median, mode };
}

const data = [1, 2, 2, 3, 4, 4, 4, 5];
const s = statistics(data);
console.log("Mean:",   s.mean);      // 3.125
console.log("Median:", s.median);    // 3.5
console.log("Mode:",   s.mode);      // [4]
JS,
            ],
            [
                'title'       => 'Sum of First N Squares',
                'difficulty'  => 'easy',
                'description' => 'Return the sum 1² + 2² + 3² + … + n² using the closed-form formula: n(n+1)(2n+1)/6.',
                'solution_code' => <<<'JS'
function sumOfSquares(n) {
    return (n * (n + 1) * (2 * n + 1)) / 6;
}

console.log(sumOfSquares(1));      // 1
console.log(sumOfSquares(3));      // 14   (1+4+9)
console.log(sumOfSquares(10));     // 385
console.log(sumOfSquares(100));    // 338350

// Verify with iteration
function sumOfSquaresIter(n) {
    let s = 0;
    for (let i = 1; i <= n; i++) s += i * i;
    return s;
}
console.log(sumOfSquares(50) === sumOfSquaresIter(50));   // true
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

const I = [[1, 0], [0, 1]];  // identity
console.log(JSON.stringify(matMul(A, I)));   // same as A
JS,
            ],
        ];
    }
}
