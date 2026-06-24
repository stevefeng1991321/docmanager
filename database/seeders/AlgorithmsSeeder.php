<?php

namespace Database\Seeders;

use App\Models\Problem;
use Illuminate\Database\Seeder;

class AlgorithmsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->problems() as $i => $p) {
            Problem::updateOrCreate(
                ['order_index' => 300 + $i + 1],
                array_merge($p, ['order_index' => 300 + $i + 1, 'category' => 'Algorithms'])
            );
        }

        $this->command->info('Seeded ' . count($this->problems()) . ' Algorithms problems (301–330).');
    }

    private function problems(): array
    {
        return [

            // ─── EASY ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Binary Search',
                'difficulty'  => 'easy',
                'description' => 'Given a sorted array and a target value, implement binary search and return the index of the target (or -1 if not found).',
                'solution_code' => <<<'JS'
function binarySearch(arr, target) {
    let left = 0, right = arr.length - 1;
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (arr[mid] === target) return mid;
        if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}

console.log(binarySearch([1, 3, 5, 7, 9, 11], 7));    // 3
console.log(binarySearch([1, 3, 5, 7, 9, 11], 6));    // -1
console.log(binarySearch([1], 1));                     // 0
JS,
            ],
            [
                'title'       => 'Linear Search',
                'difficulty'  => 'easy',
                'description' => 'Search for a target value in an unsorted array by checking each element. Return its index, or -1 if not found.',
                'solution_code' => <<<'JS'
function linearSearch(arr, target) {
    for (let i = 0; i < arr.length; i++) {
        if (arr[i] === target) return i;
    }
    return -1;
}

console.log(linearSearch([4, 2, 9, 1, 7], 9));    // 2
console.log(linearSearch([4, 2, 9, 1, 7], 5));    // -1
console.log(linearSearch([], 1));                  // -1
JS,
            ],
            [
                'title'       => 'Selection Sort',
                'difficulty'  => 'easy',
                'description' => 'Implement Selection Sort: repeatedly find the minimum element from the unsorted part and place it at the beginning. O(n²) time, O(1) space.',
                'solution_code' => <<<'JS'
function selectionSort(arr) {
    const a = [...arr];
    const n = a.length;
    for (let i = 0; i < n - 1; i++) {
        let minIdx = i;
        for (let j = i + 1; j < n; j++) {
            if (a[j] < a[minIdx]) minIdx = j;
        }
        if (minIdx !== i) [a[i], a[minIdx]] = [a[minIdx], a[i]];
    }
    return a;
}

console.log(selectionSort([64, 25, 12, 22, 11]));   // [11, 12, 22, 25, 64]
console.log(selectionSort([5, 4, 3, 2, 1]));         // [1, 2, 3, 4, 5]
JS,
            ],
            [
                'title'       => 'Insertion Sort',
                'difficulty'  => 'easy',
                'description' => 'Implement Insertion Sort: build the sorted array one element at a time by inserting each element into its correct position. O(n²) worst case, O(n) best case (already sorted).',
                'solution_code' => <<<'JS'
function insertionSort(arr) {
    const a = [...arr];
    for (let i = 1; i < a.length; i++) {
        const key = a[i];
        let j = i - 1;
        while (j >= 0 && a[j] > key) {
            a[j + 1] = a[j];
            j--;
        }
        a[j + 1] = key;
    }
    return a;
}

console.log(insertionSort([12, 11, 13, 5, 6]));   // [5, 6, 11, 12, 13]
console.log(insertionSort([1, 2, 3, 4, 5]));       // [1, 2, 3, 4, 5] (best case)
JS,
            ],
            [
                'title'       => 'Find Peak Element',
                'difficulty'  => 'easy',
                'description' => 'A peak element is one that is strictly greater than its neighbors. Find any peak index in O(log n) using binary search. Assume arr[-1] = arr[n] = -∞.',
                'solution_code' => <<<'JS'
function findPeak(arr) {
    let left = 0, right = arr.length - 1;
    while (left < right) {
        const mid = Math.floor((left + right) / 2);
        if (arr[mid] < arr[mid + 1]) left = mid + 1;
        else right = mid;
    }
    return left;
}

const a = [1, 2, 3, 1];
console.log(findPeak(a), "→", a[findPeak(a)]);    // 2 → 3

const b = [1, 2, 1, 3, 5, 6, 4];
const p = findPeak(b);
console.log(p, "→", b[p]);    // 5 → 6  (or 1 → 2, both valid peaks)

console.log(findPeak([1]));    // 0
JS,
            ],
            [
                'title'       => 'Counting Sort',
                'difficulty'  => 'easy',
                'description' => 'Sort an array of non-negative integers in O(n + k) time using counting sort, where k is the maximum value.',
                'solution_code' => <<<'JS'
function countingSort(arr) {
    if (!arr.length) return [];
    const max = Math.max(...arr);
    const count = new Array(max + 1).fill(0);
    for (const n of arr) count[n]++;
    const result = [];
    for (let i = 0; i <= max; i++) {
        while (count[i]-- > 0) result.push(i);
    }
    return result;
}

console.log(countingSort([4, 2, 2, 8, 3, 3, 1]));   // [1, 2, 2, 3, 3, 4, 8]
console.log(countingSort([0, 5, 3, 5, 0, 2]));       // [0, 0, 2, 3, 5, 5]
JS,
            ],
            [
                'title'       => 'First and Last Position in Sorted Array',
                'difficulty'  => 'easy',
                'description' => 'Given a sorted array and a target, return [firstIndex, lastIndex] of the target. Return [-1, -1] if not found. Use two binary searches for O(log n).',
                'solution_code' => <<<'JS'
function searchRange(arr, target) {
    function bound(isLeft) {
        let lo = 0, hi = arr.length - 1, idx = -1;
        while (lo <= hi) {
            const mid = (lo + hi) >> 1;
            if (arr[mid] === target) {
                idx = mid;
                if (isLeft) hi = mid - 1;
                else lo = mid + 1;
            } else if (arr[mid] < target) lo = mid + 1;
            else hi = mid - 1;
        }
        return idx;
    }
    return [bound(true), bound(false)];
}

console.log(searchRange([5,7,7,8,8,10], 8));   // [3, 4]
console.log(searchRange([5,7,7,8,8,10], 6));   // [-1, -1]
console.log(searchRange([1,1,1,1,1], 1));      // [0, 4]
JS,
            ],
            [
                'title'       => 'Sliding Window – Maximum Sum Subarray of Size K',
                'difficulty'  => 'easy',
                'description' => 'Given an array and an integer k, find the maximum sum of any contiguous subarray of size k using the sliding window technique in O(n).',
                'solution_code' => <<<'JS'
function maxSumSubarray(arr, k) {
    if (arr.length < k) return null;
    let windowSum = arr.slice(0, k).reduce((a, b) => a + b, 0);
    let maxSum = windowSum;
    for (let i = k; i < arr.length; i++) {
        windowSum += arr[i] - arr[i - k];
        maxSum = Math.max(maxSum, windowSum);
    }
    return maxSum;
}

console.log(maxSumSubarray([2, 1, 5, 1, 3, 2], 3));     // 9  (5+1+3)
console.log(maxSumSubarray([2, 3, 4, 1, 5], 2));         // 7  (3+4)
console.log(maxSumSubarray([-1, -2, -3, -4], 2));        // -3 (-1+-2)
JS,
            ],
            [
                'title'       => 'Two Pointers – Remove Duplicates from Sorted Array',
                'difficulty'  => 'easy',
                'description' => 'Given a sorted array, remove duplicates in-place using two pointers and return the new length. The relative order of unique elements must be maintained.',
                'solution_code' => <<<'JS'
function removeDuplicates(arr) {
    if (!arr.length) return 0;
    let slow = 0;
    for (let fast = 1; fast < arr.length; fast++) {
        if (arr[fast] !== arr[slow]) {
            slow++;
            arr[slow] = arr[fast];
        }
    }
    return slow + 1;
}

const a = [1, 1, 2, 2, 3, 4, 4, 5];
const len = removeDuplicates(a);
console.log(len);           // 5
console.log(a.slice(0, len)); // [1, 2, 3, 4, 5]

const b = [0, 0, 1, 1, 1, 2, 2, 3, 3, 4];
const len2 = removeDuplicates(b);
console.log(len2);            // 5
console.log(b.slice(0, len2)); // [0, 1, 2, 3, 4]
JS,
            ],
            [
                'title'       => 'Two Pointers – Move Zeros to End',
                'difficulty'  => 'easy',
                'description' => 'Move all zeros in an array to the end while preserving the relative order of non-zero elements. Do it in-place with O(1) extra space.',
                'solution_code' => <<<'JS'
function moveZeros(arr) {
    let insertPos = 0;
    for (let i = 0; i < arr.length; i++) {
        if (arr[i] !== 0) arr[insertPos++] = arr[i];
    }
    while (insertPos < arr.length) arr[insertPos++] = 0;
    return arr;
}

console.log(moveZeros([0, 1, 0, 3, 12]));     // [1, 3, 12, 0, 0]
console.log(moveZeros([0, 0, 1]));             // [1, 0, 0]
console.log(moveZeros([1, 2, 3]));             // [1, 2, 3]
JS,
            ],

            // ─── MEDIUM ──────────────────────────────────────────────────────────────

            [
                'title'       => 'Maximum Subarray (Kadane\'s Algorithm)',
                'difficulty'  => 'medium',
                'description' => 'Given an integer array, find the contiguous subarray with the largest sum and return its sum.',
                'solution_code' => <<<'JS'
function maxSubArray(nums) {
    let maxSum = nums[0];
    let currentSum = nums[0];
    for (let i = 1; i < nums.length; i++) {
        currentSum = Math.max(nums[i], currentSum + nums[i]);
        maxSum = Math.max(maxSum, currentSum);
    }
    return maxSum;
}

console.log(maxSubArray([-2, 1, -3, 4, -1, 2, 1, -5, 4]));    // 6
console.log(maxSubArray([1]));                                   // 1
console.log(maxSubArray([-2, -1]));                              // -1
JS,
            ],
            [
                'title'       => 'Merge Sort',
                'difficulty'  => 'medium',
                'description' => 'Implement the Merge Sort algorithm to sort an array of numbers in ascending order. O(n log n) time, O(n) space.',
                'solution_code' => <<<'JS'
function mergeSort(arr) {
    if (arr.length <= 1) return arr;
    const mid = Math.floor(arr.length / 2);
    const left  = mergeSort(arr.slice(0, mid));
    const right = mergeSort(arr.slice(mid));
    return merge(left, right);
}

function merge(left, right) {
    const result = [];
    let i = 0, j = 0;
    while (i < left.length && j < right.length) {
        if (left[i] <= right[j]) result.push(left[i++]);
        else result.push(right[j++]);
    }
    return result.concat(left.slice(i)).concat(right.slice(j));
}

console.log(mergeSort([38, 27, 43, 3, 9, 82, 10]));
// [3, 9, 10, 27, 38, 43, 82]
JS,
            ],
            [
                'title'       => 'Quick Sort',
                'difficulty'  => 'medium',
                'description' => 'Implement the Quick Sort algorithm using a median-of-three pivot strategy. O(n log n) average, O(n²) worst case.',
                'solution_code' => <<<'JS'
function quickSort(arr) {
    if (arr.length <= 1) return arr;
    const pivot = arr[Math.floor(arr.length / 2)];
    const left  = arr.filter(x => x < pivot);
    const mid   = arr.filter(x => x === pivot);
    const right = arr.filter(x => x > pivot);
    return [...quickSort(left), ...mid, ...quickSort(right)];
}

console.log(quickSort([3, 6, 8, 10, 1, 2, 1]));   // [1, 1, 2, 3, 6, 8, 10]
console.log(quickSort([5, 4, 3, 2, 1]));            // [1, 2, 3, 4, 5]
JS,
            ],
            [
                'title'       => 'Stack Implementation',
                'difficulty'  => 'medium',
                'description' => 'Implement a Stack data structure with push, pop, peek, and isEmpty methods.',
                'solution_code' => <<<'JS'
class Stack {
    constructor() { this.items = []; }

    push(item)  { this.items.push(item); }
    pop()       { return this.items.pop(); }
    peek()      { return this.items[this.items.length - 1]; }
    isEmpty()   { return this.items.length === 0; }
    size()      { return this.items.length; }
}

const stack = new Stack();
stack.push(1);
stack.push(2);
stack.push(3);
console.log(stack.peek());     // 3
console.log(stack.pop());      // 3
console.log(stack.size());     // 2
console.log(stack.isEmpty());  // false
JS,
            ],
            [
                'title'       => 'Queue Implementation',
                'difficulty'  => 'medium',
                'description' => 'Implement a Queue with O(1) enqueue and dequeue using an object-based approach with head and tail pointers.',
                'solution_code' => <<<'JS'
class Queue {
    constructor() { this.items = {}; this.head = 0; this.tail = 0; }

    enqueue(item) { this.items[this.tail++] = item; }
    dequeue() {
        if (this.isEmpty()) return undefined;
        const item = this.items[this.head];
        delete this.items[this.head++];
        return item;
    }
    front()   { return this.items[this.head]; }
    isEmpty() { return this.head === this.tail; }
    size()    { return this.tail - this.head; }
}

const q = new Queue();
q.enqueue("a"); q.enqueue("b"); q.enqueue("c");
console.log(q.dequeue());   // "a"
console.log(q.front());     // "b"
console.log(q.size());      // 2
JS,
            ],
            [
                'title'       => 'Reverse Linked List',
                'difficulty'  => 'medium',
                'description' => 'Reverse a singly linked list in place and return the new head.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.next = null; }
}

function buildList(arr) {
    let head = null, tail = null;
    for (const v of arr) {
        const n = new Node(v);
        if (!tail) head = tail = n;
        else { tail.next = n; tail = n; }
    }
    return head;
}

function toArray(head) {
    const arr = [];
    while (head) { arr.push(head.val); head = head.next; }
    return arr;
}

function reverseList(head) {
    let prev = null, curr = head;
    while (curr) {
        const next = curr.next;
        curr.next = prev;
        prev = curr;
        curr = next;
    }
    return prev;
}

const head = buildList([1, 2, 3, 4, 5]);
console.log(toArray(reverseList(head)));    // [5, 4, 3, 2, 1]
JS,
            ],
            [
                'title'       => 'Binary Search Tree – Insert & Search',
                'difficulty'  => 'medium',
                'description' => 'Implement a Binary Search Tree with iterative insert and search methods.',
                'solution_code' => <<<'JS'
class BSTNode {
    constructor(val) { this.val = val; this.left = this.right = null; }
}

class BST {
    constructor() { this.root = null; }

    insert(val) {
        const node = new BSTNode(val);
        if (!this.root) { this.root = node; return; }
        let curr = this.root;
        while (true) {
            if (val < curr.val) {
                if (!curr.left) { curr.left = node; return; }
                curr = curr.left;
            } else {
                if (!curr.right) { curr.right = node; return; }
                curr = curr.right;
            }
        }
    }

    search(val) {
        let curr = this.root;
        while (curr) {
            if (val === curr.val) return true;
            curr = val < curr.val ? curr.left : curr.right;
        }
        return false;
    }
}

const bst = new BST();
[5, 3, 7, 1, 4].forEach(v => bst.insert(v));
console.log(bst.search(4));    // true
console.log(bst.search(6));    // false
JS,
            ],
            [
                'title'       => 'Level Order Traversal (BFS)',
                'difficulty'  => 'medium',
                'description' => 'Return the level order traversal of a binary tree as an array of arrays (one array per level).',
                'solution_code' => <<<'JS'
class Node {
    constructor(val, left = null, right = null) {
        this.val = val; this.left = left; this.right = right;
    }
}

function levelOrder(root) {
    if (!root) return [];
    const result = [], queue = [root];
    while (queue.length) {
        const level = [];
        const size = queue.length;
        for (let i = 0; i < size; i++) {
            const node = queue.shift();
            level.push(node.val);
            if (node.left) queue.push(node.left);
            if (node.right) queue.push(node.right);
        }
        result.push(level);
    }
    return result;
}

const tree = new Node(3, new Node(9), new Node(20, new Node(15), new Node(7)));
console.log(JSON.stringify(levelOrder(tree)));
// [[3],[9,20],[15,7]]
JS,
            ],
            [
                'title'       => 'Graph BFS',
                'difficulty'  => 'medium',
                'description' => 'Implement Breadth-First Search on an undirected graph represented as an adjacency list. Return the traversal order.',
                'solution_code' => <<<'JS'
function bfs(graph, start) {
    const visited = new Set([start]);
    const queue = [start];
    const order = [];
    while (queue.length) {
        const node = queue.shift();
        order.push(node);
        for (const neighbor of (graph[node] || [])) {
            if (!visited.has(neighbor)) {
                visited.add(neighbor);
                queue.push(neighbor);
            }
        }
    }
    return order;
}

const graph = {
    A: ["B", "C"],
    B: ["A", "D", "E"],
    C: ["A", "F"],
    D: ["B"], E: ["B", "F"], F: ["C", "E"],
};
console.log(bfs(graph, "A"));    // ["A", "B", "C", "D", "E", "F"]
JS,
            ],
            [
                'title'       => 'Topological Sort',
                'difficulty'  => 'medium',
                'description' => "Given a directed acyclic graph, return a valid topological ordering using Kahn's algorithm (BFS-based). Returns [] if the graph has a cycle.",
                'solution_code' => <<<'JS'
function topoSort(numNodes, edges) {
    const inDegree = new Array(numNodes).fill(0);
    const adj = Array.from({ length: numNodes }, () => []);

    for (const [u, v] of edges) {
        adj[u].push(v);
        inDegree[v]++;
    }

    const queue = [];
    for (let i = 0; i < numNodes; i++) {
        if (inDegree[i] === 0) queue.push(i);
    }

    const order = [];
    while (queue.length) {
        const node = queue.shift();
        order.push(node);
        for (const neighbor of adj[node]) {
            if (--inDegree[neighbor] === 0) queue.push(neighbor);
        }
    }
    return order.length === numNodes ? order : [];
}

console.log(topoSort(6, [[5,2],[5,0],[4,0],[4,1],[2,3],[3,1]]));
// [4, 5, 0, 2, 1, 3] (one valid order)
JS,
            ],

            // ─── HARD ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Word Search',
                'difficulty'  => 'hard',
                'description' => 'Given a 2D grid of characters and a word, determine if the word exists by connecting adjacent cells (up/down/left/right). Each cell may only be used once per path.',
                'solution_code' => <<<'JS'
function exist(board, word) {
    const rows = board.length, cols = board[0].length;

    function dfs(r, c, idx) {
        if (idx === word.length) return true;
        if (r < 0 || r >= rows || c < 0 || c >= cols || board[r][c] !== word[idx]) return false;
        const tmp = board[r][c];
        board[r][c] = "#";
        const found = dfs(r+1,c,idx+1) || dfs(r-1,c,idx+1) || dfs(r,c+1,idx+1) || dfs(r,c-1,idx+1);
        board[r][c] = tmp;
        return found;
    }

    for (let r = 0; r < rows; r++)
        for (let c = 0; c < cols; c++)
            if (dfs(r, c, 0)) return true;
    return false;
}

const board = [["A","B","C","E"],["S","F","C","S"],["A","D","E","E"]];
console.log(exist(board, "ABCCED"));    // true
console.log(exist(board, "SEE"));       // true
console.log(exist(board, "ABCB"));      // false
JS,
            ],
            [
                'title'       => 'N-Queens',
                'difficulty'  => 'hard',
                'description' => 'Place n queens on an n×n chessboard so no two queens attack each other. Return all valid board configurations using backtracking with O(1) conflict detection via sets.',
                'solution_code' => <<<'JS'
function solveNQueens(n) {
    const results = [];
    const cols = new Set(), diag1 = new Set(), diag2 = new Set();
    const board = Array.from({ length: n }, () => Array(n).fill("."));

    function backtrack(row) {
        if (row === n) {
            results.push(board.map(r => r.join("")));
            return;
        }
        for (let col = 0; col < n; col++) {
            if (cols.has(col) || diag1.has(row - col) || diag2.has(row + col)) continue;
            cols.add(col); diag1.add(row - col); diag2.add(row + col);
            board[row][col] = "Q";
            backtrack(row + 1);
            board[row][col] = ".";
            cols.delete(col); diag1.delete(row - col); diag2.delete(row + col);
        }
    }
    backtrack(0);
    return results;
}

const sol4 = solveNQueens(4);
console.log(sol4.length);    // 2
console.log(sol4[0]);        // [".Q..","...Q","Q...","..Q."]
JS,
            ],
            [
                'title'       => 'Trapping Rain Water',
                'difficulty'  => 'hard',
                'description' => 'Given an array of heights representing an elevation map, compute how much water it can trap after raining. Use the two-pointer approach for O(n) time and O(1) space.',
                'solution_code' => <<<'JS'
function trap(height) {
    let left = 0, right = height.length - 1;
    let leftMax = 0, rightMax = 0, water = 0;
    while (left < right) {
        if (height[left] < height[right]) {
            if (height[left] >= leftMax) leftMax = height[left];
            else water += leftMax - height[left];
            left++;
        } else {
            if (height[right] >= rightMax) rightMax = height[right];
            else water += rightMax - height[right];
            right--;
        }
    }
    return water;
}

console.log(trap([0,1,0,2,1,0,1,3,2,1,2,1]));    // 6
console.log(trap([4,2,0,3,2,5]));                  // 9
JS,
            ],
            [
                'title'       => 'Largest Rectangle in Histogram',
                'difficulty'  => 'hard',
                'description' => 'Find the area of the largest rectangle that fits entirely within a histogram. Use a monotonic stack for O(n) time.',
                'solution_code' => <<<'JS'
function largestRectangle(heights) {
    const stack = [];
    let maxArea = 0;
    heights = [...heights, 0];
    for (let i = 0; i < heights.length; i++) {
        let start = i;
        while (stack.length && stack[stack.length - 1][1] > heights[i]) {
            const [idx, h] = stack.pop();
            maxArea = Math.max(maxArea, h * (i - idx));
            start = idx;
        }
        stack.push([start, heights[i]]);
    }
    return maxArea;
}

console.log(largestRectangle([2,1,5,6,2,3]));    // 10
console.log(largestRectangle([2,4]));              // 4
console.log(largestRectangle([6,2,5,4,5,1,6]));   // 12
JS,
            ],
            [
                'title'       => 'Search in Rotated Sorted Array',
                'difficulty'  => 'hard',
                'description' => 'A sorted array was rotated at an unknown pivot. Search for a target in O(log n) using a modified binary search that identifies the sorted half at each step.',
                'solution_code' => <<<'JS'
function searchRotated(nums, target) {
    let left = 0, right = nums.length - 1;
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (nums[mid] === target) return mid;
        if (nums[left] <= nums[mid]) {
            if (nums[left] <= target && target < nums[mid]) right = mid - 1;
            else left = mid + 1;
        } else {
            if (nums[mid] < target && target <= nums[right]) left = mid + 1;
            else right = mid - 1;
        }
    }
    return -1;
}

console.log(searchRotated([4, 5, 6, 7, 0, 1, 2], 0));    // 4
console.log(searchRotated([4, 5, 6, 7, 0, 1, 2], 3));    // -1
console.log(searchRotated([1], 0));                        // -1
JS,
            ],
            [
                'title'       => 'Merge K Sorted Arrays',
                'difficulty'  => 'hard',
                'description' => 'Merge k sorted arrays into one sorted array. Use a min-heap approach: track the current front of each array and always extract the minimum.',
                'solution_code' => <<<'JS'
function mergeKSorted(arrays) {
    // Min-heap via array + sort (for clarity; a real heap would be O(n log k))
    const result = [];
    // Entries: [value, arrayIndex, elementIndex]
    const heap = [];
    for (let i = 0; i < arrays.length; i++) {
        if (arrays[i].length > 0) heap.push([arrays[i][0], i, 0]);
    }
    heap.sort((a, b) => a[0] - b[0]);

    while (heap.length) {
        const [val, ai, ei] = heap.shift();
        result.push(val);
        if (ei + 1 < arrays[ai].length) {
            // insert next element from same array, keep sorted
            const next = [arrays[ai][ei + 1], ai, ei + 1];
            let pos = heap.findIndex(x => x[0] > next[0]);
            if (pos === -1) heap.push(next);
            else heap.splice(pos, 0, next);
        }
    }
    return result;
}

console.log(mergeKSorted([[1,4,7],[2,5,8],[3,6,9]]));
// [1, 2, 3, 4, 5, 6, 7, 8, 9]
console.log(mergeKSorted([[1,2,3],[4,5,6],[7,8,9]]));
// [1, 2, 3, 4, 5, 6, 7, 8, 9]
JS,
            ],
            [
                'title'       => 'Find All Permutations',
                'difficulty'  => 'hard',
                'description' => 'Given an array of distinct integers, return all possible permutations using backtracking.',
                'solution_code' => <<<'JS'
function permutations(nums) {
    const result = [];
    function backtrack(current, remaining) {
        if (!remaining.length) { result.push([...current]); return; }
        for (let i = 0; i < remaining.length; i++) {
            current.push(remaining[i]);
            backtrack(current, [...remaining.slice(0, i), ...remaining.slice(i + 1)]);
            current.pop();
        }
    }
    backtrack([], nums);
    return result;
}

const perms = permutations([1, 2, 3]);
console.log(perms.length);               // 6
console.log(JSON.stringify(perms[0]));   // [1,2,3]
console.log(JSON.stringify(perms[5]));   // [3,2,1]
JS,
            ],
            [
                'title'       => "Dijkstra's Shortest Path",
                'difficulty'  => 'hard',
                'description' => "Find the shortest path from a source node to all other nodes in a weighted graph using Dijkstra's algorithm.",
                'solution_code' => <<<'JS'
function dijkstra(graph, src) {
    const dist = {};
    const visited = new Set();
    for (const node in graph) dist[node] = Infinity;
    dist[src] = 0;
    const pq = [[0, src]];

    while (pq.length) {
        pq.sort((a, b) => a[0] - b[0]);
        const [d, u] = pq.shift();
        if (visited.has(u)) continue;
        visited.add(u);
        for (const [v, weight] of (graph[u] || [])) {
            if (dist[u] + weight < dist[v]) {
                dist[v] = dist[u] + weight;
                pq.push([dist[v], v]);
            }
        }
    }
    return dist;
}

const graph = {
    A: [["B", 1], ["C", 4]],
    B: [["C", 2], ["D", 5]],
    C: [["D", 1]],
    D: [],
};
console.log(dijkstra(graph, "A"));
// { A: 0, B: 1, C: 3, D: 4 }
JS,
            ],
            [
                'title'       => 'Trie – Insert and Search',
                'difficulty'  => 'hard',
                'description' => 'Implement a Trie (prefix tree) with insert, search, and startsWith methods. Used in autocomplete, spell checking, and IP routing.',
                'solution_code' => <<<'JS'
class TrieNode {
    constructor() { this.children = {}; this.isEnd = false; }
}

class Trie {
    constructor() { this.root = new TrieNode(); }

    insert(word) {
        let node = this.root;
        for (const ch of word) {
            if (!node.children[ch]) node.children[ch] = new TrieNode();
            node = node.children[ch];
        }
        node.isEnd = true;
    }

    search(word) {
        let node = this.root;
        for (const ch of word) {
            if (!node.children[ch]) return false;
            node = node.children[ch];
        }
        return node.isEnd;
    }

    startsWith(prefix) {
        let node = this.root;
        for (const ch of prefix) {
            if (!node.children[ch]) return false;
            node = node.children[ch];
        }
        return true;
    }
}

const trie = new Trie();
trie.insert("apple");
console.log(trie.search("apple"));     // true
console.log(trie.search("app"));       // false
console.log(trie.startsWith("app"));   // true
trie.insert("app");
console.log(trie.search("app"));       // true
JS,
            ],
            [
                'title'       => 'Max Heap Implementation',
                'difficulty'  => 'hard',
                'description' => 'Implement a Max Heap from scratch with insert (bubble-up) and extractMax (sink-down) operations. Both run in O(log n).',
                'solution_code' => <<<'JS'
class MaxHeap {
    constructor() { this.heap = []; }

    insert(val) {
        this.heap.push(val);
        this._bubbleUp(this.heap.length - 1);
    }

    extractMax() {
        if (this.heap.length === 1) return this.heap.pop();
        const max = this.heap[0];
        this.heap[0] = this.heap.pop();
        this._sinkDown(0);
        return max;
    }

    _bubbleUp(i) {
        while (i > 0) {
            const parent = Math.floor((i - 1) / 2);
            if (this.heap[parent] >= this.heap[i]) break;
            [this.heap[parent], this.heap[i]] = [this.heap[i], this.heap[parent]];
            i = parent;
        }
    }

    _sinkDown(i) {
        const n = this.heap.length;
        while (true) {
            let largest = i;
            const left = 2 * i + 1, right = 2 * i + 2;
            if (left < n && this.heap[left] > this.heap[largest]) largest = left;
            if (right < n && this.heap[right] > this.heap[largest]) largest = right;
            if (largest === i) break;
            [this.heap[largest], this.heap[i]] = [this.heap[i], this.heap[largest]];
            i = largest;
        }
    }
}

const h = new MaxHeap();
[3, 10, 1, 7, 15, 4].forEach(v => h.insert(v));
console.log(h.extractMax());    // 15
console.log(h.extractMax());    // 10
console.log(h.extractMax());    // 7
JS,
            ],
        ];
    }
}
