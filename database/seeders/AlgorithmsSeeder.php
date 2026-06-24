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

        $this->command->info('Seeded ' . count($this->problems()) . ' Algorithms problems (301–339).');
    }

    private function problems(): array
    {
        return [

            // ─── EASY ───────────────────────────────────────────────────────────────

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

            // ─── MEDIUM – SORTING & CLASSIC ALGORITHMS ───────────────────────────────

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
                'description' => 'Implement the Merge Sort algorithm to sort an array of numbers in ascending order.',
                'solution_code' => <<<'JS'
function mergeSort(arr) {
    if (arr.length <= 1) return arr;
    const mid = Math.floor(arr.length / 2);
    const left = mergeSort(arr.slice(0, mid));
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
                'description' => 'Implement the Quick Sort algorithm to sort an array of numbers.',
                'solution_code' => <<<'JS'
function quickSort(arr) {
    if (arr.length <= 1) return arr;
    const pivot = arr[Math.floor(arr.length / 2)];
    const left  = arr.filter(x => x < pivot);
    const mid   = arr.filter(x => x === pivot);
    const right = arr.filter(x => x > pivot);
    return [...quickSort(left), ...mid, ...quickSort(right)];
}

console.log(quickSort([3, 6, 8, 10, 1, 2, 1]));    // [1, 1, 2, 3, 6, 8, 10]
console.log(quickSort([5, 4, 3, 2, 1]));             // [1, 2, 3, 4, 5]
JS,
            ],
            [
                'title'       => 'Bubble Sort',
                'difficulty'  => 'medium',
                'description' => 'Implement the Bubble Sort algorithm. Optimize by stopping early if no swaps occurred in a pass.',
                'solution_code' => <<<'JS'
function bubbleSort(arr) {
    const a = [...arr];
    const n = a.length;
    for (let i = 0; i < n - 1; i++) {
        let swapped = false;
        for (let j = 0; j < n - i - 1; j++) {
            if (a[j] > a[j + 1]) {
                [a[j], a[j + 1]] = [a[j + 1], a[j]];
                swapped = true;
            }
        }
        if (!swapped) break;
    }
    return a;
}

console.log(bubbleSort([64, 34, 25, 12, 22, 11, 90]));
// [11, 12, 22, 25, 34, 64, 90]
JS,
            ],

            // ─── MEDIUM – DATA STRUCTURES ─────────────────────────────────────────────

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
                'description' => 'Implement a Queue data structure with O(1) enqueue and dequeue using an object-based approach.',
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
q.enqueue("a");
q.enqueue("b");
q.enqueue("c");
console.log(q.dequeue());    // "a"
console.log(q.front());      // "b"
console.log(q.size());       // 2
JS,
            ],
            [
                'title'       => 'Linked List – Append & Traverse',
                'difficulty'  => 'medium',
                'description' => 'Implement a singly linked list with append and toArray methods.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.next = null; }
}

class LinkedList {
    constructor() { this.head = null; }

    append(val) {
        const node = new Node(val);
        if (!this.head) { this.head = node; return; }
        let curr = this.head;
        while (curr.next) curr = curr.next;
        curr.next = node;
    }

    toArray() {
        const arr = [];
        let curr = this.head;
        while (curr) { arr.push(curr.val); curr = curr.next; }
        return arr;
    }
}

const list = new LinkedList();
list.append(1);
list.append(2);
list.append(3);
console.log(list.toArray());    // [1, 2, 3]
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
                'title'       => 'Detect Cycle in Linked List',
                'difficulty'  => 'medium',
                'description' => 'Detect if a linked list has a cycle using Floyd\'s Tortoise and Hare algorithm.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.next = null; }
}

function hasCycle(head) {
    let slow = head, fast = head;
    while (fast && fast.next) {
        slow = slow.next;
        fast = fast.next.next;
        if (slow === fast) return true;
    }
    return false;
}

// List with cycle: 1 → 2 → 3 → 4 → 2
const n1 = new Node(1), n2 = new Node(2), n3 = new Node(3), n4 = new Node(4);
n1.next = n2; n2.next = n3; n3.next = n4; n4.next = n2;
console.log(hasCycle(n1));    // true

// No cycle
const a = new Node(1), b = new Node(2);
a.next = b;
console.log(hasCycle(a));     // false
JS,
            ],
            [
                'title'       => 'Merge Two Sorted Linked Lists',
                'difficulty'  => 'medium',
                'description' => 'Merge two sorted linked lists and return the merged list head.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.next = null; }
}

function mergeLists(l1, l2) {
    const dummy = new Node(0);
    let curr = dummy;
    while (l1 && l2) {
        if (l1.val <= l2.val) { curr.next = l1; l1 = l1.next; }
        else { curr.next = l2; l2 = l2.next; }
        curr = curr.next;
    }
    curr.next = l1 || l2;
    return dummy.next;
}

function build(arr) {
    let head = null, tail = null;
    for (const v of arr) {
        const n = new Node(v);
        if (!tail) head = tail = n;
        else { tail.next = n; tail = n; }
    }
    return head;
}
function toArr(h) { const a = []; while (h) { a.push(h.val); h = h.next; } return a; }

console.log(toArr(mergeLists(build([1,3,5]), build([2,4,6]))));
// [1, 2, 3, 4, 5, 6]
JS,
            ],
            [
                'title'       => 'Binary Search Tree – Insert & Search',
                'difficulty'  => 'medium',
                'description' => 'Implement a Binary Search Tree with insert and search methods.',
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
                'title'       => 'BST Inorder Traversal',
                'difficulty'  => 'medium',
                'description' => 'Perform an inorder traversal of a Binary Search Tree and return the values in sorted order.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.left = this.right = null; }
}

function insert(root, val) {
    if (!root) return new Node(val);
    if (val < root.val) root.left = insert(root.left, val);
    else root.right = insert(root.right, val);
    return root;
}

function inorder(root, result = []) {
    if (!root) return result;
    inorder(root.left, result);
    result.push(root.val);
    inorder(root.right, result);
    return result;
}

let root = null;
for (const v of [5, 3, 7, 1, 4, 6, 8]) root = insert(root, v);
console.log(inorder(root));    // [1, 3, 4, 5, 6, 7, 8]
JS,
            ],
            [
                'title'       => 'Tree Height (Max Depth)',
                'difficulty'  => 'medium',
                'description' => 'Find the maximum depth (height) of a binary tree.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val, left = null, right = null) {
        this.val = val; this.left = left; this.right = right;
    }
}

function maxDepth(root) {
    if (!root) return 0;
    return 1 + Math.max(maxDepth(root.left), maxDepth(root.right));
}

const tree = new Node(1,
    new Node(2, new Node(4), new Node(5)),
    new Node(3)
);
console.log(maxDepth(tree));    // 3
console.log(maxDepth(null));    // 0
JS,
            ],
            [
                'title'       => 'Level Order Traversal (BFS)',
                'difficulty'  => 'medium',
                'description' => 'Return the level order traversal of a binary tree as an array of arrays (one per level).',
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

            // ─── MEDIUM – GRAPHS ──────────────────────────────────────────────────────

            [
                'title'       => 'Graph BFS',
                'difficulty'  => 'medium',
                'description' => 'Implement Breadth-First Search on a graph represented as an adjacency list.',
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
    D: ["B"],
    E: ["B", "F"],
    F: ["C", "E"],
};
console.log(bfs(graph, "A"));    // ["A", "B", "C", "D", "E", "F"]
JS,
            ],
            [
                'title'       => 'Graph DFS',
                'difficulty'  => 'medium',
                'description' => 'Implement Depth-First Search on a graph represented as an adjacency list.',
                'solution_code' => <<<'JS'
function dfs(graph, start, visited = new Set(), order = []) {
    visited.add(start);
    order.push(start);
    for (const neighbor of (graph[start] || [])) {
        if (!visited.has(neighbor)) {
            dfs(graph, neighbor, visited, order);
        }
    }
    return order;
}

const graph = {
    A: ["B", "C"],
    B: ["A", "D", "E"],
    C: ["A", "F"],
    D: ["B"],
    E: ["B", "F"],
    F: ["C", "E"],
};
console.log(dfs(graph, "A"));    // ["A", "B", "D", "E", "F", "C"]
JS,
            ],
            [
                'title'       => 'Number of Islands',
                'difficulty'  => 'medium',
                'description' => 'Given a 2D grid of "1" (land) and "0" (water), count the number of islands using DFS flood fill.',
                'solution_code' => <<<'JS'
function numIslands(grid) {
    let count = 0;
    function sink(r, c) {
        if (r < 0 || r >= grid.length || c < 0 || c >= grid[0].length || grid[r][c] !== "1") return;
        grid[r][c] = "0";
        sink(r+1,c); sink(r-1,c); sink(r,c+1); sink(r,c-1);
    }
    for (let r = 0; r < grid.length; r++) {
        for (let c = 0; c < grid[0].length; c++) {
            if (grid[r][c] === "1") { count++; sink(r, c); }
        }
    }
    return count;
}

const grid = [
    ["1","1","0","0","0"],
    ["1","1","0","0","0"],
    ["0","0","1","0","0"],
    ["0","0","0","1","1"],
];
console.log(numIslands(grid));    // 3
JS,
            ],
            [
                'title'       => 'Flood Fill',
                'difficulty'  => 'medium',
                'description' => 'Implement the flood fill algorithm: given a starting pixel, replace its color and all connected same-colored pixels with a new color.',
                'solution_code' => <<<'JS'
function floodFill(image, sr, sc, newColor) {
    const originalColor = image[sr][sc];
    if (originalColor === newColor) return image;

    function fill(r, c) {
        if (r < 0 || r >= image.length || c < 0 || c >= image[0].length) return;
        if (image[r][c] !== originalColor) return;
        image[r][c] = newColor;
        fill(r+1,c); fill(r-1,c);
        fill(r,c+1); fill(r,c-1);
    }
    fill(sr, sc);
    return image;
}

const image = [[1,1,1],[1,1,0],[1,0,1]];
console.log(JSON.stringify(floodFill(image, 1, 1, 2)));
// [[2,2,2],[2,2,0],[2,0,1]]
JS,
            ],
            [
                'title'       => 'Topological Sort',
                'difficulty'  => 'medium',
                'description' => "Given a directed acyclic graph as a list of edges, return a valid topological ordering using Kahn's algorithm.",
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
    return order.length === numNodes ? order : [];  // [] if cycle
}

console.log(topoSort(6, [[5,2],[5,0],[4,0],[4,1],[2,3],[3,1]]));
// one valid order: [4, 5, 0, 2, 1, 3]
JS,
            ],
            [
                'title'       => 'LRU Cache',
                'difficulty'  => 'medium',
                'description' => 'Implement a Least Recently Used (LRU) Cache with O(1) get and put operations using JavaScript\'s Map (insertion-order guaranteed).',
                'solution_code' => <<<'JS'
class LRUCache {
    constructor(capacity) {
        this.capacity = capacity;
        this.cache = new Map();
    }

    get(key) {
        if (!this.cache.has(key)) return -1;
        const val = this.cache.get(key);
        this.cache.delete(key);
        this.cache.set(key, val);
        return val;
    }

    put(key, value) {
        if (this.cache.has(key)) this.cache.delete(key);
        else if (this.cache.size >= this.capacity) {
            this.cache.delete(this.cache.keys().next().value);  // evict LRU
        }
        this.cache.set(key, value);
    }
}

const cache = new LRUCache(2);
cache.put(1, 1);
cache.put(2, 2);
console.log(cache.get(1));    // 1
cache.put(3, 3);               // evicts key 2
console.log(cache.get(2));    // -1
JS,
            ],

            // ─── HARD ───────────────────────────────────────────────────────────────

            [
                'title'       => 'Word Search',
                'difficulty'  => 'hard',
                'description' => 'Given a 2D grid of characters and a word, determine if the word exists by connecting adjacent cells (up/down/left/right). Each cell may only be used once.',
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
                'title'       => 'Jump Game II (Minimum Jumps)',
                'difficulty'  => 'hard',
                'description' => 'Given an array where each element is the max jump from that position, return the minimum number of jumps to reach the last index.',
                'solution_code' => <<<'JS'
function jump(nums) {
    let jumps = 0, currentEnd = 0, farthest = 0;
    for (let i = 0; i < nums.length - 1; i++) {
        farthest = Math.max(farthest, i + nums[i]);
        if (i === currentEnd) {
            jumps++;
            currentEnd = farthest;
        }
    }
    return jumps;
}

console.log(jump([2, 3, 1, 1, 4]));    // 2
console.log(jump([2, 3, 0, 1, 4]));    // 2
console.log(jump([1, 2, 3]));           // 2
JS,
            ],
            [
                'title'       => 'Merge K Sorted Arrays',
                'difficulty'  => 'hard',
                'description' => 'Merge k sorted arrays into one sorted array.',
                'solution_code' => <<<'JS'
function mergeKSorted(arrays) {
    const result = [];
    const pointers = new Array(arrays.length).fill(0);
    while (true) {
        let minVal = Infinity, minIdx = -1;
        for (let i = 0; i < arrays.length; i++) {
            if (pointers[i] < arrays[i].length && arrays[i][pointers[i]] < minVal) {
                minVal = arrays[i][pointers[i]];
                minIdx = i;
            }
        }
        if (minIdx === -1) break;
        result.push(minVal);
        pointers[minIdx]++;
    }
    return result;
}

console.log(mergeKSorted([[1,4,7],[2,5,8],[3,6,9]]));
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
console.log(perms.length);              // 6
console.log(JSON.stringify(perms[0]));  // [1,2,3]
console.log(JSON.stringify(perms[5]));  // [3,2,1]
JS,
            ],
            [
                'title'       => 'Power Set (All Subsets)',
                'difficulty'  => 'hard',
                'description' => 'Given an array of distinct integers, return all possible subsets (the power set).',
                'solution_code' => <<<'JS'
function subsets(nums) {
    const result = [[]];
    for (const n of nums) {
        const newSubsets = result.map(sub => [...sub, n]);
        result.push(...newSubsets);
    }
    return result;
}

console.log(JSON.stringify(subsets([1, 2, 3])));
// [[],[1],[2],[1,2],[3],[1,3],[2,3],[1,2,3]]
console.log(subsets([1, 2, 3]).length);   // 8 = 2^3
JS,
            ],
            [
                'title'       => 'Spiral Matrix',
                'difficulty'  => 'hard',
                'description' => 'Given an m×n matrix, return all elements in spiral order.',
                'solution_code' => <<<'JS'
function spiralOrder(matrix) {
    const result = [];
    let top = 0, bottom = matrix.length - 1;
    let left = 0, right = matrix[0].length - 1;
    while (top <= bottom && left <= right) {
        for (let i = left; i <= right; i++) result.push(matrix[top][i]);
        top++;
        for (let i = top; i <= bottom; i++) result.push(matrix[i][right]);
        right--;
        if (top <= bottom) {
            for (let i = right; i >= left; i--) result.push(matrix[bottom][i]);
            bottom--;
        }
        if (left <= right) {
            for (let i = bottom; i >= top; i--) result.push(matrix[i][left]);
            left++;
        }
    }
    return result;
}

console.log(spiralOrder([[1,2,3],[4,5,6],[7,8,9]]));
// [1,2,3,6,9,8,7,4,5]
JS,
            ],
            [
                'title'       => 'Rotate Matrix 90 Degrees',
                'difficulty'  => 'hard',
                'description' => 'Rotate an n×n matrix 90 degrees clockwise in place. Approach: transpose then reverse each row.',
                'solution_code' => <<<'JS'
function rotateMatrix(matrix) {
    const n = matrix.length;
    for (let i = 0; i < n; i++)
        for (let j = i + 1; j < n; j++)
            [matrix[i][j], matrix[j][i]] = [matrix[j][i], matrix[i][j]];
    for (let i = 0; i < n; i++)
        matrix[i].reverse();
    return matrix;
}

const m = [[1,2,3],[4,5,6],[7,8,9]];
console.log(JSON.stringify(rotateMatrix(m)));
// [[7,4,1],[8,5,2],[9,6,3]]
JS,
            ],
            [
                'title'       => 'Search in Rotated Sorted Array',
                'difficulty'  => 'hard',
                'description' => 'A sorted array was rotated at some pivot. Search for a target and return its index (or -1).',
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
                'title'       => 'Trapping Rain Water',
                'difficulty'  => 'hard',
                'description' => 'Given an array of heights representing an elevation map, compute how much water it can trap after raining.',
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
                'description' => 'Find the largest rectangle that can be formed in a histogram (array of bar heights).',
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
JS,
            ],
            [
                'title'       => 'First Missing Positive',
                'difficulty'  => 'hard',
                'description' => 'Find the smallest missing positive integer in an unsorted array. Must run in O(n) time and O(1) extra space.',
                'solution_code' => <<<'JS'
function firstMissingPositive(nums) {
    const n = nums.length;
    for (let i = 0; i < n; i++) {
        while (nums[i] > 0 && nums[i] <= n && nums[nums[i] - 1] !== nums[i]) {
            [nums[nums[i] - 1], nums[i]] = [nums[i], nums[nums[i] - 1]];
        }
    }
    for (let i = 0; i < n; i++) {
        if (nums[i] !== i + 1) return i + 1;
    }
    return n + 1;
}

console.log(firstMissingPositive([1, 2, 0]));      // 3
console.log(firstMissingPositive([3, 4, -1, 1]));  // 2
console.log(firstMissingPositive([7, 8, 9]));       // 1
JS,
            ],
            [
                'title'       => 'Median of Two Sorted Arrays',
                'difficulty'  => 'hard',
                'description' => 'Find the median of two sorted arrays in O(log(min(m,n))) time using binary search on the shorter array.',
                'solution_code' => <<<'JS'
function findMedianSortedArrays(nums1, nums2) {
    if (nums1.length > nums2.length) return findMedianSortedArrays(nums2, nums1);
    const m = nums1.length, n = nums2.length;
    let lo = 0, hi = m;
    while (lo <= hi) {
        const px = Math.floor((lo + hi) / 2);
        const py = Math.floor((m + n + 1) / 2) - px;
        const maxLeftX  = px === 0 ? -Infinity : nums1[px - 1];
        const minRightX = px === m ?  Infinity : nums1[px];
        const maxLeftY  = py === 0 ? -Infinity : nums2[py - 1];
        const minRightY = py === n ?  Infinity : nums2[py];
        if (maxLeftX <= minRightY && maxLeftY <= minRightX) {
            if ((m + n) % 2 === 0)
                return (Math.max(maxLeftX, maxLeftY) + Math.min(minRightX, minRightY)) / 2;
            return Math.max(maxLeftX, maxLeftY);
        } else if (maxLeftX > minRightY) hi = px - 1;
        else lo = px + 1;
    }
}

console.log(findMedianSortedArrays([1,3],[2]));      // 2
console.log(findMedianSortedArrays([1,2],[3,4]));    // 2.5
JS,
            ],
            [
                'title'       => 'N-Queens',
                'difficulty'  => 'hard',
                'description' => 'Place n queens on an n×n chessboard so no two queens attack each other. Return all valid board configurations.',
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

const solutions = solveNQueens(4);
console.log(solutions.length);    // 2
console.log(solutions[0]);        // [".Q..","...Q","Q...","..Q."]
JS,
            ],
            [
                'title'       => 'Serialize and Deserialize BST',
                'difficulty'  => 'hard',
                'description' => 'Implement functions to serialize a Binary Search Tree to a string and deserialize it back to a BST.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.left = this.right = null; }
}

function serialize(root) {
    const vals = [];
    function preorder(node) {
        if (!node) { vals.push("null"); return; }
        vals.push(node.val);
        preorder(node.left);
        preorder(node.right);
    }
    preorder(root);
    return vals.join(",");
}

function deserialize(data) {
    const vals = data.split(",");
    let idx = 0;
    function build() {
        if (vals[idx] === "null") { idx++; return null; }
        const node = new Node(Number(vals[idx++]));
        node.left = build();
        node.right = build();
        return node;
    }
    return build();
}

function insert(root, val) {
    if (!root) return new Node(val);
    if (val < root.val) root.left = insert(root.left, val);
    else root.right = insert(root.right, val);
    return root;
}

let root = null;
for (const v of [5, 3, 7, 1, 4]) root = insert(root, v);
const str = serialize(root);
console.log(str);
const rebuilt = deserialize(str);
console.log(serialize(rebuilt) === str);    // true
JS,
            ],
            [
                'title'       => 'Trie – Insert and Search',
                'difficulty'  => 'hard',
                'description' => 'Implement a Trie (prefix tree) with insert, search, and startsWith methods.',
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
                'description' => 'Implement a Max Heap with insert and extractMax operations.',
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
JS,
            ],
            [
                'title'       => "Dijkstra's Shortest Path",
                'difficulty'  => 'hard',
                'description' => "Given a weighted graph as an adjacency list, find the shortest path from a source node to all other nodes using Dijkstra's algorithm.",
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
                'title'       => 'Lowest Common Ancestor (BST)',
                'difficulty'  => 'hard',
                'description' => 'Find the Lowest Common Ancestor of two nodes in a Binary Search Tree using the BST property.',
                'solution_code' => <<<'JS'
class Node {
    constructor(val) { this.val = val; this.left = this.right = null; }
}

function lca(root, p, q) {
    while (root) {
        if (p < root.val && q < root.val) root = root.left;
        else if (p > root.val && q > root.val) root = root.right;
        else return root.val;
    }
    return null;
}

function insert(root, val) {
    if (!root) return new Node(val);
    if (val < root.val) root.left = insert(root.left, val);
    else root.right = insert(root.right, val);
    return root;
}

let root = null;
for (const v of [6, 2, 8, 0, 4, 7, 9, 3, 5]) root = insert(root, v);
console.log(lca(root, 2, 8));    // 6
console.log(lca(root, 2, 4));    // 2
JS,
            ],
        ];
    }
}
