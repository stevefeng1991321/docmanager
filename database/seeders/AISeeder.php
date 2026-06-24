<?php

namespace Database\Seeders;

use App\Models\Problem;
use Illuminate\Database\Seeder;

class AISeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->problems() as $i => $p) {
            Problem::updateOrCreate(
                ['order_index' => 400 + $i + 1],
                array_merge($p, ['order_index' => 400 + $i + 1, 'category' => 'AI'])
            );
        }

        $this->command->info('Seeded ' . count($this->problems()) . ' AI problems (401–410).');
    }

    private function problems(): array
    {
        return [
            [
                'title'       => 'Sigmoid Activation Function',
                'difficulty'  => 'easy',
                'description' => 'Implement the sigmoid activation function σ(x) = 1 / (1 + e^−x). It maps any real number to a value between 0 and 1 and is widely used in neural network output layers for binary classification. Also implement its derivative σ\'(x) = σ(x) × (1 − σ(x)), which is used during backpropagation.',
                'solution_code' => <<<'JS'
function sigmoid(x) {
    return 1 / (1 + Math.exp(-x));
}

function sigmoidDerivative(x) {
    const s = sigmoid(x);
    return s * (1 - s);
}

function sigmoidArray(xs) {
    return xs.map(sigmoid);
}

console.log(sigmoid(0));              // 0.5
console.log(sigmoid(2));              // 0.8808 (close to 1)
console.log(sigmoid(-2));             // 0.1192 (close to 0)
console.log(sigmoidDerivative(0));    // 0.25 (max gradient at x=0)

const logits = [-3, -1, 0, 1, 3];
console.log(sigmoidArray(logits).map(v => +v.toFixed(4)));
// [0.0474, 0.2689, 0.5, 0.7311, 0.9526]
JS,
            ],
            [
                'title'       => 'ReLU Activation Function',
                'difficulty'  => 'easy',
                'description' => 'Implement the Rectified Linear Unit (ReLU) activation function: f(x) = max(0, x). ReLU is the most widely used activation function in deep learning hidden layers because it avoids the vanishing gradient problem. Also implement Leaky ReLU: f(x) = x if x > 0, else α × x (where α is a small constant like 0.01).',
                'solution_code' => <<<'JS'
function relu(x) {
    return Math.max(0, x);
}

function leakyRelu(x, alpha = 0.01) {
    return x > 0 ? x : alpha * x;
}

function reluArray(xs) {
    return xs.map(relu);
}

function leakyReluArray(xs, alpha = 0.01) {
    return xs.map(x => leakyRelu(x, alpha));
}

const values = [-4, -1, 0, 1, 4];

console.log(reluArray(values));
// [0, 0, 0, 1, 4]

console.log(leakyReluArray(values));
// [-0.04, -0.01, 0, 1, 4]

function reluDerivative(x) {
    return x > 0 ? 1 : 0;
}

console.log(values.map(reluDerivative));
// [0, 0, 0, 1, 1]
JS,
            ],
            [
                'title'       => 'Softmax Function',
                'difficulty'  => 'easy',
                'description' => 'Implement the Softmax function, which converts a vector of raw scores (logits) into a probability distribution where all values sum to 1. Used in the final layer of multi-class classification neural networks. Formula: softmax(x_i) = e^x_i / Σ e^x_j. Subtract the max for numerical stability.',
                'solution_code' => <<<'JS'
function softmax(logits) {
    const maxLogit = Math.max(...logits);
    const exps = logits.map(x => Math.exp(x - maxLogit));
    const sumExps = exps.reduce((a, b) => a + b, 0);
    return exps.map(e => e / sumExps);
}

const scores = [2.0, 1.0, 0.5];
const probs = softmax(scores);
console.log(probs.map(p => +p.toFixed(4)));
// [0.6364, 0.2341, 0.1295]

const total = probs.reduce((a, b) => a + b, 0);
console.log(+total.toFixed(10));    // 1

// Numerical stability test with large values
console.log(softmax([1000, 1001, 1002]).map(p => +p.toFixed(4)));
// [0.0900, 0.2447, 0.6652]
JS,
            ],
            [
                'title'       => 'Cosine Similarity',
                'difficulty'  => 'easy',
                'description' => 'Implement cosine similarity between two vectors. It measures the cosine of the angle between them, returning a value from -1 (opposite) to 1 (identical direction). Widely used in NLP to compare text embeddings. Formula: cos(A, B) = (A · B) / (||A|| × ||B||)',
                'solution_code' => <<<'JS'
function cosineSimilarity(a, b) {
    if (a.length !== b.length) throw new Error("Vectors must have same length");

    const dot  = a.reduce((sum, ai, i) => sum + ai * b[i], 0);
    const magA = Math.sqrt(a.reduce((sum, ai) => sum + ai * ai, 0));
    const magB = Math.sqrt(b.reduce((sum, bi) => sum + bi * bi, 0));

    if (magA === 0 || magB === 0) return 0;
    return dot / (magA * magB);
}

console.log(+cosineSimilarity([1, 2, 3], [1, 2, 3]).toFixed(4));      // 1
console.log(+cosineSimilarity([1, 0], [0, 1]).toFixed(4));             // 0
console.log(+cosineSimilarity([1, 2], [-1, -2]).toFixed(4));           // -1
console.log(+cosineSimilarity([1, 3, 2, 5], [1, 2, 3, 4]).toFixed(4)); // ~0.9818
JS,
            ],
            [
                'title'       => 'Loss Functions: MSE and Binary Cross-Entropy',
                'difficulty'  => 'easy',
                'description' => 'Implement two fundamental loss functions:\n\n1. Mean Squared Error (MSE) — for regression: MSE = (1/n) Σ(y − ŷ)²\n\n2. Binary Cross-Entropy (BCE) — for binary classification: BCE = -(1/n) Σ[y·log(ŷ) + (1−y)·log(1−ŷ)]',
                'solution_code' => <<<'JS'
function mse(actual, predicted) {
    const n = actual.length;
    return actual.reduce((sum, y, i) => sum + Math.pow(y - predicted[i], 2), 0) / n;
}

function binaryCrossEntropy(actual, predicted) {
    const eps = 1e-15;
    const n = actual.length;
    return -actual.reduce((sum, y, i) => {
        const p = Math.min(Math.max(predicted[i], eps), 1 - eps);
        return sum + y * Math.log(p) + (1 - y) * Math.log(1 - p);
    }, 0) / n;
}

const actual    = [1, 0, 1, 1, 0];
const predicted = [0.9, 0.1, 0.8, 0.7, 0.2];

console.log(+mse(actual, predicted).toFixed(4));                 // 0.03
console.log(+binaryCrossEntropy(actual, predicted).toFixed(4));  // 0.1965

const perfect = [0.9999, 0.0001, 0.9999, 0.9999, 0.0001];
console.log(+binaryCrossEntropy(actual, perfect).toFixed(4));    // ~0.0001
JS,
            ],
            [
                'title'       => 'K-Nearest Neighbors (KNN) Classifier',
                'difficulty'  => 'medium',
                'description' => 'Implement the K-Nearest Neighbors classification algorithm. For each query point, find the k closest labeled training points using Euclidean distance, then return the majority class label among those neighbors.',
                'solution_code' => <<<'JS'
function euclidean(a, b) {
    return Math.sqrt(a.reduce((sum, ai, i) => sum + (ai - b[i]) ** 2, 0));
}

function knnClassify(trainPoints, trainLabels, query, k = 3) {
    const distances = trainPoints.map((point, i) => ({
        distance: euclidean(point, query),
        label:    trainLabels[i],
    }));

    distances.sort((a, b) => a.distance - b.distance);
    const nearestK = distances.slice(0, k);

    const votes = {};
    for (const { label } of nearestK) {
        votes[label] = (votes[label] || 0) + 1;
    }
    return Object.entries(votes).sort((a, b) => b[1] - a[1])[0][0];
}

const points = [[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const labels = ["A","A","A","B","B","B"];

console.log(knnClassify(points, labels, [2, 2], 3));      // "A"
console.log(knnClassify(points, labels, [5, 4], 3));      // "B"
console.log(knnClassify(points, labels, [1.5, 1.5], 1));  // "A"
JS,
            ],
            [
                'title'       => 'Simple Linear Regression',
                'difficulty'  => 'medium',
                'description' => 'Implement simple linear regression using the Ordinary Least Squares (OLS) closed-form solution. Given paired (x, y) data, compute the best-fit line y = mx + b that minimizes the sum of squared errors.',
                'solution_code' => <<<'JS'
function linearRegression(xs, ys) {
    const n = xs.length;
    const sumX  = xs.reduce((a, b) => a + b, 0);
    const sumY  = ys.reduce((a, b) => a + b, 0);
    const sumXY = xs.reduce((s, x, i) => s + x * ys[i], 0);
    const sumX2 = xs.reduce((s, x) => s + x * x, 0);

    const m = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX ** 2);
    const b = (sumY - m * sumX) / n;

    const meanY = sumY / n;
    const ssTot = ys.reduce((s, y) => s + (y - meanY) ** 2, 0);
    const ssRes = xs.reduce((s, x, i) => s + (ys[i] - (m * x + b)) ** 2, 0);

    return {
        slope:     +m.toFixed(4),
        intercept: +b.toFixed(4),
        rSquared:  +(1 - ssRes / ssTot).toFixed(4),
        predict:   x => m * x + b,
    };
}

const sizes  = [600, 800, 1000, 1200, 1400, 1600];
const prices = [150, 200,  250,  300,  350,  400];

const model = linearRegression(sizes, prices);
console.log("Slope:",     model.slope);       // 0.125
console.log("Intercept:", model.intercept);   // 75
console.log("R²:",        model.rSquared);    // 1
console.log("Predict 1300 sqft: $" + model.predict(1300) + "k");  // 237.5
JS,
            ],
            [
                'title'       => 'Gradient Descent',
                'difficulty'  => 'medium',
                'description' => 'Implement gradient descent to minimize a function. Starting from an initial guess, iteratively move in the direction of the negative gradient scaled by the learning rate. Apply it to minimize f(x) = (x − 3)², whose minimum is at x = 3.',
                'solution_code' => <<<'JS'
function gradientDescent(gradientFn, initialX, learningRate = 0.1, iterations = 100) {
    let x = initialX;
    const history = [];

    for (let i = 0; i < iterations; i++) {
        const grad = gradientFn(x);
        x = x - learningRate * grad;
        history.push({ iteration: i + 1, x: +x.toFixed(6) });
    }

    return { minimum: +x.toFixed(6), history };
}

// Minimize f(x) = (x - 3)^2, gradient = 2(x - 3)
const gradient = x => 2 * (x - 3);
const result = gradientDescent(gradient, 10, 0.1, 50);

console.log("Minimum found at x =", result.minimum);    // ≈ 3

[0, 9, 24, 49].forEach(i => {
    const h = result.history[i];
    console.log(`Iter ${h.iteration}: x = ${h.x}`);
});
// Iter 1:  x = 8.6
// Iter 10: x ≈ 4.27
// Iter 25: x ≈ 3.05
// Iter 50: x ≈ 3.0001
JS,
            ],
            [
                'title'       => 'Perceptron (Single Neuron Classifier)',
                'difficulty'  => 'medium',
                'description' => 'Implement the Perceptron learning algorithm — the simplest form of a neural network. It learns to classify linearly separable data by adjusting weights based on misclassified examples. Update rule: w = w + learningRate × (y − ŷ) × x.',
                'solution_code' => <<<'JS'
function trainPerceptron(data, labels, learningRate = 0.1, epochs = 100) {
    const nFeatures = data[0].length;
    let weights = new Array(nFeatures).fill(0);
    let bias = 0;

    function predict(x) {
        const z = x.reduce((sum, xi, i) => sum + xi * weights[i], bias);
        return z >= 0 ? 1 : 0;
    }

    for (let epoch = 0; epoch < epochs; epoch++) {
        let errors = 0;
        for (let j = 0; j < data.length; j++) {
            const yHat = predict(data[j]);
            const error = labels[j] - yHat;
            if (error !== 0) {
                errors++;
                weights = weights.map((w, i) => w + learningRate * error * data[j][i]);
                bias += learningRate * error;
            }
        }
        if (errors === 0) {
            console.log(`Converged at epoch ${epoch + 1}`);
            break;
        }
    }

    return { weights, bias, predict };
}

// Learn logical AND
const data   = [[0,0],[0,1],[1,0],[1,1]];
const labels = [0, 0, 0, 1];

const model = trainPerceptron(data, labels, 0.1, 100);
console.log(model.predict([0, 0]));    // 0
console.log(model.predict([0, 1]));    // 0
console.log(model.predict([1, 0]));    // 0
console.log(model.predict([1, 1]));    // 1
JS,
            ],
            [
                'title'       => 'Feedforward Neural Network (Forward Pass)',
                'difficulty'  => 'hard',
                'description' => 'Implement the forward pass of a 2-layer feedforward neural network (one hidden layer). Given input features, weight matrices W1/W2, and biases b1/b2, compute the network output using ReLU for the hidden layer and Sigmoid for the output.',
                'solution_code' => <<<'JS'
function matMul(matrix, vector) {
    return matrix.map(row => row.reduce((sum, w, i) => sum + w * vector[i], 0));
}
function addBias(vec, bias) {
    return vec.map((v, i) => v + bias[i]);
}
function relu(vec) {
    return vec.map(x => Math.max(0, x));
}
function sigmoid(x) {
    return 1 / (1 + Math.exp(-x));
}

function forwardPass(input, W1, b1, W2, b2) {
    const z1 = addBias(matMul(W1, input), b1);
    const a1 = relu(z1);

    const z2 = addBias(matMul(W2, a1), b2);
    const output = z2.map(sigmoid);

    return { hiddenActivations: a1, output };
}

// Network: 2 inputs → 3 hidden neurons → 1 output
const W1 = [[0.5, -0.2], [0.1, 0.8], [-0.3, 0.6]];
const b1 = [0.1, -0.1, 0.2];
const W2 = [[0.4, 0.7, -0.5]];
const b2 = [0.1];

const input = [1.0, 0.5];
const result = forwardPass(input, W1, b1, W2, b2);

console.log("Hidden activations:", result.hiddenActivations.map(v => +v.toFixed(4)));
console.log("Output (probability):", result.output.map(v => +v.toFixed(4)));
JS,
            ],
        ];
    }
}
