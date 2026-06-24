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

        $this->command->info('Seeded ' . count($this->problems()) . ' AI problems (401–430).');
    }

    private function problems(): array
    {
        return [

            // ─── EASY ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Sigmoid Activation Function',
                'difficulty'  => 'easy',
                'description' => 'Implement the sigmoid function σ(x) = 1 / (1 + e^−x) and its derivative σ\'(x) = σ(x)(1 − σ(x)). Sigmoid squashes any real number to (0, 1) and is used in binary classification output layers and during backpropagation.',
                'solution_code' => <<<'JS'
function sigmoid(x) {
    return 1 / (1 + Math.exp(-x));
}

function sigmoidDerivative(x) {
    const s = sigmoid(x);
    return s * (1 - s);
}

console.log(sigmoid(0));              // 0.5
console.log(sigmoid(2).toFixed(4));   // 0.8808
console.log(sigmoid(-2).toFixed(4));  // 0.1192
console.log(sigmoidDerivative(0));    // 0.25  (max gradient at x=0)

const logits = [-3, -1, 0, 1, 3];
console.log(logits.map(x => +sigmoid(x).toFixed(4)));
// [0.0474, 0.2689, 0.5, 0.7311, 0.9526]
JS,
            ],
            [
                'title'       => 'ReLU Activation Function',
                'difficulty'  => 'easy',
                'description' => 'Implement ReLU f(x) = max(0, x) and Leaky ReLU f(x) = x if x > 0 else α·x. ReLU avoids the vanishing gradient problem and is the default activation in hidden layers.',
                'solution_code' => <<<'JS'
function relu(x) { return Math.max(0, x); }
function leakyRelu(x, alpha = 0.01) { return x > 0 ? x : alpha * x; }
function reluDerivative(x) { return x > 0 ? 1 : 0; }

const values = [-4, -1, 0, 1, 4];

console.log(values.map(relu));                   // [0, 0, 0, 1, 4]
console.log(values.map(x => leakyRelu(x)));      // [-0.04, -0.01, 0, 1, 4]
console.log(values.map(reluDerivative));          // [0, 0, 0, 1, 1]
JS,
            ],
            [
                'title'       => 'Softmax Function',
                'difficulty'  => 'easy',
                'description' => 'Implement Softmax: convert raw logits into a probability distribution where all outputs sum to 1. Subtract the max logit before exponentiation for numerical stability.',
                'solution_code' => <<<'JS'
function softmax(logits) {
    const max = Math.max(...logits);
    const exps = logits.map(x => Math.exp(x - max));
    const sum = exps.reduce((a, b) => a + b, 0);
    return exps.map(e => e / sum);
}

const scores = [2.0, 1.0, 0.5];
const probs = softmax(scores);
console.log(probs.map(p => +p.toFixed(4)));
// [0.6364, 0.2341, 0.1295]
console.log(probs.reduce((a, b) => a + b, 0).toFixed(6));  // 1.000000

// Numerical stability test with large values
console.log(softmax([1000, 1001, 1002]).map(p => +p.toFixed(4)));
// [0.0900, 0.2447, 0.6652]
JS,
            ],
            [
                'title'       => 'Cosine Similarity',
                'difficulty'  => 'easy',
                'description' => 'Compute cosine similarity between two vectors: cos(A,B) = (A·B) / (||A|| × ||B||). Returns a value in [-1, 1]. Widely used in NLP to compare word or sentence embeddings.',
                'solution_code' => <<<'JS'
function cosineSimilarity(a, b) {
    const dot  = a.reduce((s, ai, i) => s + ai * b[i], 0);
    const magA = Math.sqrt(a.reduce((s, ai) => s + ai * ai, 0));
    const magB = Math.sqrt(b.reduce((s, bi) => s + bi * bi, 0));
    if (magA === 0 || magB === 0) return 0;
    return dot / (magA * magB);
}

console.log(+cosineSimilarity([1,2,3], [1,2,3]).toFixed(4));       // 1     (identical)
console.log(+cosineSimilarity([1,0], [0,1]).toFixed(4));            // 0     (orthogonal)
console.log(+cosineSimilarity([1,2], [-1,-2]).toFixed(4));          // -1    (opposite)
console.log(+cosineSimilarity([1,3,2,5], [1,2,3,4]).toFixed(4));   // 0.9818
JS,
            ],
            [
                'title'       => 'Loss Functions: MSE and Binary Cross-Entropy',
                'difficulty'  => 'easy',
                'description' => 'Implement two fundamental loss functions. Mean Squared Error (MSE) for regression: (1/n)Σ(y−ŷ)². Binary Cross-Entropy (BCE) for binary classification: -(1/n)Σ[y·log(ŷ)+(1−y)·log(1−ŷ)]. Clip predictions to avoid log(0).',
                'solution_code' => <<<'JS'
function mse(actual, predicted) {
    return actual.reduce((s, y, i) => s + (y - predicted[i]) ** 2, 0) / actual.length;
}

function binaryCrossEntropy(actual, predicted) {
    const eps = 1e-15;
    return -actual.reduce((s, y, i) => {
        const p = Math.min(Math.max(predicted[i], eps), 1 - eps);
        return s + y * Math.log(p) + (1 - y) * Math.log(1 - p);
    }, 0) / actual.length;
}

const actual    = [1, 0, 1, 1, 0];
const predicted = [0.9, 0.1, 0.8, 0.7, 0.2];

console.log(+mse(actual, predicted).toFixed(4));                // 0.03
console.log(+binaryCrossEntropy(actual, predicted).toFixed(4)); // 0.1965
JS,
            ],
            [
                'title'       => 'Euclidean and Manhattan Distance',
                'difficulty'  => 'easy',
                'description' => 'Implement Euclidean distance (straight-line, L2 norm) and Manhattan distance (city-block, L1 norm) between two vectors. These are the core distance metrics used in KNN, K-Means, and many other ML algorithms.',
                'solution_code' => <<<'JS'
function euclidean(a, b) {
    return Math.sqrt(a.reduce((s, ai, i) => s + (ai - b[i]) ** 2, 0));
}

function manhattan(a, b) {
    return a.reduce((s, ai, i) => s + Math.abs(ai - b[i]), 0);
}

console.log(euclidean([0,0], [3,4]));     // 5
console.log(manhattan([0,0], [3,4]));     // 7

console.log(euclidean([1,2,3], [4,6,3])); // 5
console.log(manhattan([1,2,3], [4,6,3])); // 7

// Minkowski generalizes both: p=2 → Euclidean, p=1 → Manhattan
function minkowski(a, b, p) {
    return Math.pow(a.reduce((s, ai, i) => s + Math.abs(ai - b[i]) ** p, 0), 1 / p);
}
console.log(minkowski([0,0], [3,4], 2));   // 5
console.log(minkowski([0,0], [3,4], 1));   // 7
JS,
            ],
            [
                'title'       => 'One-Hot Encoding',
                'difficulty'  => 'easy',
                'description' => 'Convert an array of categorical labels into one-hot encoded vectors. Each label maps to a binary vector with a 1 at the label\'s class index and 0 everywhere else.',
                'solution_code' => <<<'JS'
function oneHotEncode(labels) {
    const classes = [...new Set(labels)].sort();
    const classIndex = Object.fromEntries(classes.map((c, i) => [c, i]));
    const encoded = labels.map(label => {
        const vec = new Array(classes.length).fill(0);
        vec[classIndex[label]] = 1;
        return vec;
    });
    return { encoded, classes };
}

const result = oneHotEncode(['cat','dog','cat','bird','dog']);
console.log('Classes:', result.classes);     // ['bird','cat','dog']
console.log('cat  →', result.encoded[0]);    // [0, 1, 0]
console.log('dog  →', result.encoded[1]);    // [0, 0, 1]
console.log('bird →', result.encoded[3]);    // [1, 0, 0]
JS,
            ],
            [
                'title'       => 'Feature Scaling: Min-Max and Z-Score',
                'difficulty'  => 'easy',
                'description' => 'Implement two feature normalization methods. Min-Max scaling maps values to [0, 1]. Z-Score standardization maps values to mean=0, std=1. Both prevent features with large ranges from dominating distance-based models.',
                'solution_code' => <<<'JS'
function minMaxScale(data) {
    const min = Math.min(...data), max = Math.max(...data);
    const range = max - min;
    return range === 0 ? data.map(() => 0) : data.map(x => (x - min) / range);
}

function zScoreScale(data) {
    const n = data.length;
    const mean = data.reduce((a, b) => a + b, 0) / n;
    const std  = Math.sqrt(data.reduce((s, x) => s + (x - mean) ** 2, 0) / n);
    return std === 0 ? data.map(() => 0) : data.map(x => (x - mean) / std);
}

const features = [100, 200, 300, 400, 500];
console.log(minMaxScale(features));
// [0, 0.25, 0.5, 0.75, 1]

console.log(zScoreScale(features).map(v => +v.toFixed(4)));
// [-1.4142, -0.7071, 0.0000, 0.7071, 1.4142]
JS,
            ],
            [
                'title'       => 'Vector and Matrix Operations',
                'difficulty'  => 'easy',
                'description' => 'Implement the core linear algebra operations used in ML: dot product, vector addition, scalar multiplication, and 2D matrix multiplication. These are the building blocks of every neural network layer.',
                'solution_code' => <<<'JS'
function dot(a, b)         { return a.reduce((s, ai, i) => s + ai * b[i], 0); }
function vecAdd(a, b)      { return a.map((ai, i) => ai + b[i]); }
function vecScale(a, k)    { return a.map(ai => ai * k); }
function vecMagnitude(a)   { return Math.sqrt(dot(a, a)); }

function matMul(A, B) {
    return A.map(row => B[0].map((_, j) => row.reduce((s, _, k) => s + row[k] * B[k][j], 0)));
}

console.log(dot([1,2,3], [4,5,6]));        // 32
console.log(vecMagnitude([3,4]));           // 5
console.log(vecAdd([1,2,3], [4,5,6]));      // [5, 7, 9]
console.log(vecScale([1,2,3], 2));          // [2, 4, 6]

const A = [[1,2],[3,4]];
const B = [[5,6],[7,8]];
console.log(matMul(A, B));   // [[19,22],[43,50]]
JS,
            ],
            [
                'title'       => 'Categorical Cross-Entropy Loss',
                'difficulty'  => 'easy',
                'description' => 'Implement categorical cross-entropy loss for multi-class classification: L = -(1/n) Σ log(ŷ[true_class]). Takes class indices as labels and a softmax probability matrix as predictions. Clip to avoid log(0).',
                'solution_code' => <<<'JS'
function categoricalCrossEntropy(trueLabels, predictions) {
    const eps = 1e-15;
    return -trueLabels.reduce((sum, label, i) => {
        const p = Math.min(Math.max(predictions[i][label], eps), 1 - eps);
        return sum + Math.log(p);
    }, 0) / trueLabels.length;
}

// trueLabels: class indices; predictions: softmax probability rows
const labels = [0, 1, 2, 1];
const preds  = [
    [0.90, 0.05, 0.05],   // correct (class 0), confident
    [0.10, 0.80, 0.10],   // correct (class 1), confident
    [0.10, 0.20, 0.70],   // correct (class 2), confident
    [0.30, 0.40, 0.30],   // correct (class 1), uncertain
];

console.log(+categoricalCrossEntropy(labels, preds).toFixed(4));   // ~0.4255

const perfect = [[1,0,0],[0,1,0],[0,0,1],[0,1,0]];
console.log(+categoricalCrossEntropy(labels, perfect).toFixed(4)); // ~0.0000
JS,
            ],

            // ─── MEDIUM ──────────────────────────────────────────────────────────────

            [
                'title'       => 'K-Nearest Neighbors (KNN) Classifier',
                'difficulty'  => 'medium',
                'description' => 'Implement KNN: for each query point, find the k closest training points using Euclidean distance and return the majority class label. KNN is a non-parametric, lazy learner — it memorizes training data and classifies at inference time.',
                'solution_code' => <<<'JS'
function euclidean(a, b) {
    return Math.sqrt(a.reduce((s, ai, i) => s + (ai - b[i]) ** 2, 0));
}

function knnClassify(trainPoints, trainLabels, query, k = 3) {
    const distances = trainPoints
        .map((pt, i) => ({ dist: euclidean(pt, query), label: trainLabels[i] }))
        .sort((a, b) => a.dist - b.dist)
        .slice(0, k);

    const votes = {};
    for (const { label } of distances) votes[label] = (votes[label] || 0) + 1;
    return Object.entries(votes).sort((a, b) => b[1] - a[1])[0][0];
}

const points = [[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const labels = ['A','A','A','B','B','B'];

console.log(knnClassify(points, labels, [2,2], 3));     // "A"
console.log(knnClassify(points, labels, [5,4], 3));     // "B"
console.log(knnClassify(points, labels, [1.5,1.5], 1)); // "A"
JS,
            ],
            [
                'title'       => 'Simple Linear Regression',
                'difficulty'  => 'medium',
                'description' => 'Implement simple linear regression using the Ordinary Least Squares closed-form solution. Given paired (x, y) observations, compute the best-fit line y = mx + b and R² goodness-of-fit.',
                'solution_code' => <<<'JS'
function linearRegression(xs, ys) {
    const n = xs.length;
    const sumX  = xs.reduce((a, b) => a + b, 0);
    const sumY  = ys.reduce((a, b) => a + b, 0);
    const sumXY = xs.reduce((s, x, i) => s + x * ys[i], 0);
    const sumX2 = xs.reduce((s, x) => s + x * x, 0);

    const m = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX ** 2);
    const b = (sumY - m * sumX) / n;

    const meanY  = sumY / n;
    const ssTot  = ys.reduce((s, y) => s + (y - meanY) ** 2, 0);
    const ssRes  = xs.reduce((s, x, i) => s + (ys[i] - (m * x + b)) ** 2, 0);

    return { slope: +m.toFixed(4), intercept: +b.toFixed(4), r2: +(1 - ssRes / ssTot).toFixed(4), predict: x => m * x + b };
}

const sizes  = [600,800,1000,1200,1400,1600];
const prices = [150,200, 250, 300, 350, 400];

const model = linearRegression(sizes, prices);
console.log('Slope:',     model.slope);       // 0.125
console.log('Intercept:', model.intercept);   // 75
console.log('R²:',        model.r2);          // 1
console.log('Predict 1300sqft: $' + model.predict(1300).toFixed(1) + 'k'); // 237.5k
JS,
            ],
            [
                'title'       => 'Gradient Descent',
                'difficulty'  => 'medium',
                'description' => 'Implement gradient descent: repeatedly move parameters in the direction of the negative gradient scaled by the learning rate. Apply it to minimize f(x) = (x − 3)², whose gradient is 2(x − 3) and global minimum is x = 3.',
                'solution_code' => <<<'JS'
function gradientDescent(gradFn, x0, lr = 0.1, iterations = 100) {
    let x = x0;
    const history = [];
    for (let i = 0; i < iterations; i++) {
        x -= lr * gradFn(x);
        history.push(+x.toFixed(6));
    }
    return { minimum: +x.toFixed(6), history };
}

// Minimize f(x) = (x-3)^2, gradient = 2(x-3)
const { minimum, history } = gradientDescent(x => 2 * (x - 3), 10, 0.1, 50);

console.log('Minimum at x =', minimum);    // ≈ 3

[0, 9, 24, 49].forEach(i => console.log(`Iter ${i+1}: x = ${history[i]}`));
// Iter  1: x = 8.6
// Iter 10: x ≈ 4.268
// Iter 25: x ≈ 3.047
// Iter 50: x ≈ 3.0001
JS,
            ],
            [
                'title'       => 'Perceptron (Single Neuron Classifier)',
                'difficulty'  => 'medium',
                'description' => 'Implement the Perceptron learning rule: for each misclassified sample update weights w += lr × (y − ŷ) × x. The Perceptron converges on linearly separable data and is the conceptual foundation of all neural networks.',
                'solution_code' => <<<'JS'
function trainPerceptron(data, labels, lr = 0.1, epochs = 100) {
    let weights = new Array(data[0].length).fill(0);
    let bias = 0;

    const predict = x => x.reduce((s, xi, i) => s + xi * weights[i], bias) >= 0 ? 1 : 0;

    for (let epoch = 0; epoch < epochs; epoch++) {
        let errors = 0;
        for (let j = 0; j < data.length; j++) {
            const error = labels[j] - predict(data[j]);
            if (error !== 0) {
                weights = weights.map((w, i) => w + lr * error * data[j][i]);
                bias += lr * error;
                errors++;
            }
        }
        if (errors === 0) { console.log(`Converged at epoch ${epoch + 1}`); break; }
    }
    return { weights, bias, predict };
}

// Learn logical AND
const data   = [[0,0],[0,1],[1,0],[1,1]];
const labels = [0, 0, 0, 1];
const model  = trainPerceptron(data, labels);
data.forEach((x, i) => console.log(`${x} → ${model.predict(x)} (expected ${labels[i]})`));
JS,
            ],
            [
                'title'       => 'Feedforward Neural Network (Forward Pass)',
                'difficulty'  => 'medium',
                'description' => 'Implement the forward pass of a two-layer network (one hidden layer). Compute z1 = W1·x + b1, apply ReLU to get a1, then z2 = W2·a1 + b2, apply Sigmoid to get the output probability.',
                'solution_code' => <<<'JS'
function matVec(W, v) { return W.map(row => row.reduce((s, w, i) => s + w * v[i], 0)); }
function addBias(z, b) { return z.map((v, i) => v + b[i]); }
function relu(z)    { return z.map(x => Math.max(0, x)); }
function sigmoid(z) { return z.map(x => 1 / (1 + Math.exp(-x))); }

function forwardPass(x, W1, b1, W2, b2) {
    const a1 = relu(addBias(matVec(W1, x), b1));
    const out = sigmoid(addBias(matVec(W2, a1), b2));
    return { a1, out };
}

// 2 inputs → 3 hidden → 1 output
const W1 = [[0.5,-0.2],[0.1,0.8],[-0.3,0.6]];
const b1 = [0.1,-0.1,0.2];
const W2 = [[0.4,0.7,-0.5]];
const b2 = [0.1];

const { a1, out } = forwardPass([1.0,0.5], W1, b1, W2, b2);
console.log('Hidden:', a1.map(v => +v.toFixed(4)));
console.log('Output:', out.map(v => +v.toFixed(4)));
JS,
            ],
            [
                'title'       => 'K-Means Clustering',
                'difficulty'  => 'medium',
                'description' => 'Implement K-Means: alternate between assigning points to the nearest centroid and recomputing centroids as cluster means until labels stop changing. Initialize centroids using the first k data points.',
                'solution_code' => <<<'JS'
function dist(a, b) { return Math.sqrt(a.reduce((s, ai, i) => s + (ai - b[i]) ** 2, 0)); }

function kMeans(data, k, maxIter = 100) {
    let centroids = data.slice(0, k).map(p => [...p]);

    function assign() {
        return data.map(pt => {
            let best = 0;
            centroids.forEach((c, j) => { if (dist(pt, c) < dist(pt, centroids[best])) best = j; });
            return best;
        });
    }

    let labels = assign();
    for (let iter = 0; iter < maxIter; iter++) {
        centroids = centroids.map((_, j) => {
            const cluster = data.filter((_, i) => labels[i] === j);
            if (!cluster.length) return centroids[j];
            return cluster[0].map((_, d) => cluster.reduce((s, p) => s + p[d], 0) / cluster.length);
        });
        const next = assign();
        if (JSON.stringify(next) === JSON.stringify(labels)) { console.log(`Converged at iter ${iter+1}`); break; }
        labels = next;
    }
    return { centroids: centroids.map(c => c.map(v => +v.toFixed(2))), labels };
}

const data = [[1,1],[1.5,2],[3,4],[5,7],[3.5,5],[4.5,5],[3.5,4.5]];
const { centroids, labels } = kMeans(data, 2);
console.log('Centroids:', centroids);
console.log('Labels:',    labels);
JS,
            ],
            [
                'title'       => 'Confusion Matrix Metrics',
                'difficulty'  => 'medium',
                'description' => 'Compute TP, FP, FN, TN from binary classification results, then derive Precision = TP/(TP+FP), Recall = TP/(TP+FN), F1 = 2·P·R/(P+R), and Accuracy. These metrics are more informative than raw accuracy on imbalanced datasets.',
                'solution_code' => <<<'JS'
function confusionMetrics(actual, predicted) {
    let tp = 0, fp = 0, fn = 0, tn = 0;
    for (let i = 0; i < actual.length; i++) {
        if      (actual[i] && predicted[i])   tp++;
        else if (!actual[i] && predicted[i])  fp++;
        else if (actual[i] && !predicted[i])  fn++;
        else                                  tn++;
    }
    const precision = tp / (tp + fp) || 0;
    const recall    = tp / (tp + fn) || 0;
    const f1        = 2 * precision * recall / (precision + recall) || 0;

    return {
        matrix:    { tp, fp, fn, tn },
        precision: +precision.toFixed(4),
        recall:    +recall.toFixed(4),
        f1:        +f1.toFixed(4),
        accuracy:  +((tp + tn) / actual.length).toFixed(4),
    };
}

const actual    = [1,1,0,1,0,1,0,0,1,0];
const predicted = [1,0,0,1,0,1,1,0,1,0];
const result = confusionMetrics(actual, predicted);
console.log('Matrix:',    result.matrix);      // {tp:5,fp:1,fn:1,tn:3}
console.log('Precision:', result.precision);   // 0.8333
console.log('Recall:',    result.recall);      // 0.8333
console.log('F1:',        result.f1);          // 0.8333
console.log('Accuracy:',  result.accuracy);    // 0.8
JS,
            ],
            [
                'title'       => 'Gaussian Naive Bayes',
                'difficulty'  => 'medium',
                'description' => 'Train a Gaussian Naive Bayes classifier by computing per-class Gaussian distributions for each feature, then classify new points by picking the class with the highest log-posterior: log P(class) + Σ log P(feature | class).',
                'solution_code' => <<<'JS'
function gaussianNB(X, y) {
    const classes = [...new Set(y)];
    const n = y.length;
    const priors = {}, stats = {};

    for (const cls of classes) {
        const idx = y.reduce((a, label, i) => label === cls ? [...a, i] : a, []);
        priors[cls] = idx.length / n;
        stats[cls] = X[0].map((_, j) => {
            const vals = idx.map(i => X[i][j]);
            const mean = vals.reduce((a, b) => a + b, 0) / vals.length;
            const variance = vals.reduce((s, v) => s + (v - mean) ** 2, 0) / vals.length + 1e-9;
            return { mean, variance };
        });
    }

    function predict(x) {
        return classes.reduce((best, cls) => {
            let logP = Math.log(priors[cls]);
            stats[cls].forEach(({ mean, variance }, j) => {
                logP += -((x[j] - mean) ** 2) / (2 * variance) - 0.5 * Math.log(2 * Math.PI * variance);
            });
            return logP > best.logP ? { cls, logP } : best;
        }, { cls: null, logP: -Infinity }).cls;
    }

    return { predict };
}

const X = [[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const y = [0, 0, 0, 1, 1, 1];
const model = gaussianNB(X, y);
console.log(model.predict([1.5, 1.5]));   // 0
console.log(model.predict([5.5, 5.5]));   // 1
JS,
            ],
            [
                'title'       => 'Logistic Regression',
                'difficulty'  => 'medium',
                'description' => 'Train logistic regression using batch gradient descent. The model outputs P(y=1|x) = σ(w·x + b). Minimize binary cross-entropy by updating weights with the gradient: (ŷ − y) · x.',
                'solution_code' => <<<'JS'
function sigmoid(x) { return 1 / (1 + Math.exp(-x)); }

function logisticRegression(X, y, lr = 0.3, epochs = 500) {
    let w = new Array(X[0].length).fill(0), b = 0;
    const predict = x => sigmoid(x.reduce((s, xi, i) => s + xi * w[i], b));

    for (let epoch = 0; epoch < epochs; epoch++) {
        const dw = new Array(w.length).fill(0);
        let db = 0;
        X.forEach((x, j) => {
            const err = predict(x) - y[j];
            x.forEach((xi, i) => dw[i] += err * xi);
            db += err;
        });
        w = w.map((wi, i) => wi - lr * dw[i] / X.length);
        b -= lr * db / X.length;
    }

    return { w, b, predict, classify: x => predict(x) >= 0.5 ? 1 : 0 };
}

const X = [[0.5,1.5],[1.5,0.5],[1,1],[5,4.5],[4.5,5],[5,5.5]];
const y = [0,0,0,1,1,1];
const model = logisticRegression(X, y);
console.log('Weights:', model.w.map(v => +v.toFixed(3)));
X.forEach((x, i) => console.log(`${x} → ${model.classify(x)} (truth: ${y[i]})`));
JS,
            ],
            [
                'title'       => 'Batch Normalization Forward Pass',
                'difficulty'  => 'medium',
                'description' => 'Implement Batch Normalization: normalize each feature across the batch to mean=0, std=1, then scale and shift with learnable parameters γ (gamma) and β (beta). This stabilizes training and allows higher learning rates.',
                'solution_code' => <<<'JS'
function batchNorm(X, gamma, beta, epsilon = 1e-8) {
    const B = X.length, F = X[0].length;

    // Per-feature mean and variance over batch
    const means = Array.from({length: F}, (_, j) => X.reduce((s, row) => s + row[j], 0) / B);
    const vars  = Array.from({length: F}, (_, j) => X.reduce((s, row) => s + (row[j] - means[j]) ** 2, 0) / B);

    const Xnorm = X.map(row => row.map((x, j) => (x - means[j]) / Math.sqrt(vars[j] + epsilon)));
    const out   = Xnorm.map(row => row.map((xn, j) => gamma[j] * xn + beta[j]));

    return { out, means, vars, Xnorm };
}

// Batch of 4 samples, 3 features
const X = [[2,3,5],[1,7,3],[3,5,1],[2,1,7]];
const gamma = [1, 1, 1];
const beta  = [0, 0, 0];

const { out, means, vars } = batchNorm(X, gamma, beta);
console.log('Means:',    means.map(v => +v.toFixed(2)));  // [2, 4, 4]
console.log('Vars:',     vars.map(v => +v.toFixed(2)));   // [0.5, 5, 4]
console.log('Row 0 normalized:', out[0].map(v => +v.toFixed(4)));
JS,
            ],

            // ─── HARD ────────────────────────────────────────────────────────────────

            [
                'title'       => 'Backpropagation (XOR Network)',
                'difficulty'  => 'hard',
                'description' => 'Train a 2-hidden-unit network to solve XOR using backpropagation. Compute forward pass, then propagate gradients backward through the sigmoid output, hidden layer weights (W2/b2), hidden activations, and input weights (W1/b1). XOR is not linearly separable so requires at least one hidden layer.',
                'solution_code' => <<<'JS'
function sig(x)  { return 1 / (1 + Math.exp(-x)); }
function sigD(x) { const s = sig(x); return s * (1 - s); }

let W1 = [[0.5,-0.3],[0.2,0.8]], b1 = [0.1,-0.1];
let W2 = [[0.4,0.7]],            b2 = [0.0];
const lr = 0.5;

const data   = [[0,0],[0,1],[1,0],[1,1]];
const labels = [0, 1, 1, 0];

for (let epoch = 0; epoch < 5000; epoch++) {
    for (let d = 0; d < 4; d++) {
        const x = data[d], y = labels[d];

        // Forward
        const z1  = W1.map((row,i) => row.reduce((s,w,j)=>s+w*x[j], b1[i]));
        const a1  = z1.map(sig);
        const z2  = [W2[0].reduce((s,w,j)=>s+w*a1[j], b2[0])];
        const out = sig(z2[0]);

        // Backward
        const dZ2 = (out - y) * sigD(z2[0]);
        const dW2 = a1.map(a => dZ2 * a);
        const dA1 = W2[0].map(w => w * dZ2);
        const dZ1 = dA1.map((dv,i) => dv * sigD(z1[i]));
        const dW1 = dZ1.map(dz => x.map(xi => dz * xi));

        // Update
        W2[0] = W2[0].map((w,j) => w - lr * dW2[j]);
        b2[0] -= lr * dZ2;
        W1 = W1.map((row,i) => row.map((w,j) => w - lr * dW1[i][j]));
        b1 = b1.map((bv,i) => bv - lr * dZ1[i]);
    }
}

function predict(x) {
    const a1 = W1.map((row,i) => sig(row.reduce((s,w,j)=>s+w*x[j], b1[i])));
    return sig(W2[0].reduce((s,w,j)=>s+w*a1[j], b2[0]));
}

data.forEach(x => console.log(`[${x}] → ${predict(x).toFixed(4)}`));
// [0,0] → ~0.03   [0,1] → ~0.97   [1,0] → ~0.97   [1,1] → ~0.03
JS,
            ],
            [
                'title'       => 'Convolutional Layer Forward Pass',
                'difficulty'  => 'hard',
                'description' => 'Implement a 2D convolution with configurable stride and zero-padding. Slide the kernel over the input, computing the sum of element-wise products at each position. This is the core computation in CNNs for image feature extraction.',
                'solution_code' => <<<'JS'
function conv2d(input, kernel, stride = 1, padding = 0) {
    const H = input.length, W = input[0].length;
    const kH = kernel.length, kW = kernel[0].length;

    // Zero-pad input
    const pad = Array.from({length: H + 2*padding}, (_, r) =>
        Array.from({length: W + 2*padding}, (_, c) => {
            const rr = r - padding, cc = c - padding;
            return (rr >= 0 && rr < H && cc >= 0 && cc < W) ? input[rr][cc] : 0;
        })
    );

    const outH = Math.floor((H + 2*padding - kH) / stride) + 1;
    const outW = Math.floor((W + 2*padding - kW) / stride) + 1;
    const out  = Array.from({length: outH}, () => new Array(outW).fill(0));

    for (let r = 0; r < outH; r++)
        for (let c = 0; c < outW; c++)
            for (let kr = 0; kr < kH; kr++)
                for (let kc = 0; kc < kW; kc++)
                    out[r][c] += pad[r*stride+kr][c*stride+kc] * kernel[kr][kc];

    return out;
}

const img = [[1,2,3,0],[0,1,2,3],[3,0,1,2],[2,3,0,1]];

// Sobel-X: detect vertical edges
const sobelX = [[-1,0,1],[-2,0,2],[-1,0,1]];
console.log('Sobel-X output:');
conv2d(img, sobelX).forEach(row => console.log(row));

// Same-size output with padding=1
const out = conv2d(img, sobelX, 1, 1);
console.log('With padding, size:', out.length, 'x', out[0].length);  // 4 x 4
JS,
            ],
            [
                'title'       => 'LSTM Cell Forward Pass',
                'difficulty'  => 'hard',
                'description' => 'Implement one LSTM time step. Four gates — forget (f), input (i), candidate cell (g), output (o) — are computed from the current input x and previous hidden state h. The cell state c accumulates long-term memory; the hidden state h carries short-term context.',
                'solution_code' => <<<'JS'
function sig(x)  { return 1 / (1 + Math.exp(-x)); }

function lstmCell(x, h, c, params) {
    const { Wf,Wi,Wg,Wo, Uf,Ui,Ug,Uo, bf,bi,bg,bo } = params;

    const f = sig(Wf*x + Uf*h + bf);          // forget gate: what to erase from c
    const i = sig(Wi*x + Ui*h + bi);          // input gate: what new info to write
    const g = Math.tanh(Wg*x + Ug*h + bg);   // candidate cell value
    const o = sig(Wo*x + Uo*h + bo);          // output gate: what to expose

    const cNext = f * c + i * g;               // new cell state
    const hNext = o * Math.tanh(cNext);        // new hidden state

    return { h: hNext, c: cNext, gates: {f,i,g,o} };
}

const params = { Wf:0.5,Wi:0.6,Wg:0.4,Wo:0.7, Uf:0.3,Ui:0.2,Ug:0.5,Uo:0.4, bf:-0.1,bi:0,bg:0.1,bo:-0.2 };
const seq = [1.0, 0.5, -0.3, 0.8];

let h = 0, c = 0;
for (const x of seq) {
    ({ h, c } = lstmCell(x, h, c, params));
    console.log(`x=${x}: h=${h.toFixed(4)}, c=${c.toFixed(4)}`);
}
JS,
            ],
            [
                'title'       => 'Scaled Dot-Product Attention (Transformer)',
                'difficulty'  => 'hard',
                'description' => 'Implement the core attention mechanism: Attention(Q,K,V) = softmax(Q·K^T / √d_k)·V. Q (queries), K (keys), and V (values) are matrices of token representations. Dividing by √d_k prevents dot products from growing too large and causing vanishing softmax gradients.',
                'solution_code' => <<<'JS'
function softmax(row) {
    const max = Math.max(...row);
    const exps = row.map(x => Math.exp(x - max));
    const sum = exps.reduce((a,b)=>a+b,0);
    return exps.map(e => e/sum);
}
function matMul(A,B) {
    return A.map(row => B[0].map((_, j) => row.reduce((s,_,k)=>s+row[k]*B[k][j],0)));
}
function transpose(M) { return M[0].map((_,j)=>M.map(row=>row[j])); }

function scaledDotProductAttention(Q, K, V) {
    const dk = Q[0].length;
    // Raw scores
    const scores = matMul(Q, transpose(K)).map(row => row.map(s => s / Math.sqrt(dk)));
    // Softmax over each row → attention weights
    const weights = scores.map(softmax);
    // Weighted sum of values
    const output = matMul(weights, V);
    return { output, weights };
}

// 3 tokens, d_k=4, d_v=2
const Q = [[1,0,1,0],[0,1,0,1],[1,1,0,0]];
const K = [[1,0,1,0],[0,1,0,1],[1,0,0,1]];
const V = [[1,0],[0,1],[1,1]];

const { output, weights } = scaledDotProductAttention(Q, K, V);
console.log('Attention weights:');
weights.forEach(row => console.log(row.map(v => +v.toFixed(3))));
console.log('Output:');
output.forEach(row => console.log(row.map(v => +v.toFixed(4))));
JS,
            ],
            [
                'title'       => 'Adam Optimizer',
                'difficulty'  => 'hard',
                'description' => 'Implement the Adam optimizer: maintain exponential moving averages of gradients (m, first moment) and squared gradients (v, second moment), apply bias correction, then update each parameter with lr · m̂ / (√v̂ + ε). Combines momentum and adaptive learning rates.',
                'solution_code' => <<<'JS'
function adamStep(params, grads, m, v, t, lr=0.001, beta1=0.9, beta2=0.999, eps=1e-8) {
    const newParams = {}, newM = {}, newV = {};
    for (const key in params) {
        newM[key] = beta1 * m[key] + (1 - beta1) * grads[key];
        newV[key] = beta2 * v[key] + (1 - beta2) * grads[key] ** 2;
        const mHat = newM[key] / (1 - beta1 ** t);
        const vHat = newV[key] / (1 - beta2 ** t);
        newParams[key] = params[key] - lr * mHat / (Math.sqrt(vHat) + eps);
    }
    return { params: newParams, m: newM, v: newV };
}

// Minimize f(w) = w^2, gradient = 2w, true minimum w=0
let state = { params: { w: 10.0 }, m: { w: 0 }, v: { w: 0 } };

for (let t = 1; t <= 20; t++) {
    const grads = { w: 2 * state.params.w };
    state = adamStep(state.params, grads, state.m, state.v, t, 0.1);
    if ([1,5,10,20].includes(t)) console.log(`Step ${t}: w = ${state.params.w.toFixed(5)}`);
}
// Step  1: w ≈ 9.9
// Step  5: w ≈ 9.5
// Step 10: w ≈ 8.9
// Step 20: w ≈ 7.6  (converging toward 0)
JS,
            ],
            [
                'title'       => 'Dropout (Inverted)',
                'difficulty'  => 'hard',
                'description' => 'Implement inverted dropout: during training, randomly zero out neurons with probability p and scale surviving activations by 1/(1-p) to keep expected values unchanged. At inference, pass activations through unchanged. Dropout is a regularization technique that prevents co-adaptation of neurons.',
                'solution_code' => <<<'JS'
function dropout(activations, rate, training = true, seed = null) {
    if (!training) return [...activations];

    const keepProb = 1 - rate;
    // Use seeded LCG for reproducibility in demo
    let rng = seed !== null ? seed : 42;
    const rand = () => { rng = (rng * 1664525 + 1013904223) & 0xFFFFFFFF; return (rng >>> 0) / 0xFFFFFFFF; };

    const mask = activations.map(() => rand() < keepProb ? 1 : 0);
    // Inverted: scale up so expected value is preserved
    const out  = activations.map((a, i) => (a * mask[i]) / keepProb);

    return { out, mask, dropped: mask.filter(m => m === 0).length };
}

const neurons = [0.5, 0.8, 0.3, 0.9, 0.6, 0.7, 0.4, 0.2];

// Training: ~50% dropout
const { out, mask, dropped } = dropout(neurons, 0.5, true);
console.log('Input: ', neurons);
console.log('Mask:  ', mask);
console.log('Output:', out.map(v => +v.toFixed(2)));
console.log(`Dropped: ${dropped}/8 neurons`);

// Inference: no dropout
console.log('Inference:', dropout(neurons, 0.5, false));
JS,
            ],
            [
                'title'       => 'Beam Search Decoder',
                'difficulty'  => 'hard',
                'description' => 'Implement beam search: at each decoding step, expand every active beam by all vocabulary tokens and keep only the top-k highest cumulative log-probability sequences. Greedy search is beam search with k=1. Wider beams trade compute for quality.',
                'solution_code' => <<<'JS'
function beamSearch(logProbFn, startToken, endToken, vocab, beamWidth = 3, maxLen = 8) {
    let beams = [{ tokens: [startToken], score: 0 }];
    const finished = [];

    for (let step = 0; step < maxLen; step++) {
        const candidates = [];
        for (const beam of beams) {
            if (beam.tokens.at(-1) === endToken) { finished.push(beam); continue; }
            const logProbs = logProbFn(beam.tokens);
            for (const token of vocab) {
                candidates.push({ tokens: [...beam.tokens, token], score: beam.score + (logProbs[token] ?? -Infinity) });
            }
        }
        candidates.sort((a, b) => b.score - a.score);
        beams = candidates.slice(0, beamWidth);
        if (beams.every(b => b.tokens.at(-1) === endToken)) break;
    }

    return [...beams, ...finished].sort((a, b) => b.score - a.score)[0];
}

// Toy bigram model
const bigrams = {
    '<s>': { the: -0.5, a: -0.9, an: -1.5, '</s>': -3 },
    the:   { cat: -0.6, dog: -0.8, </s>: -2 },
    a:     { cat: -0.4, dog: -1.2, </s>: -2 },
    an:    { ant: -0.3, eel: -0.9, </s>: -2 },
    cat:   { sat: -0.5, ran: -1.0, '</s>': -0.8 },
    dog:   { ran: -0.4, sat: -1.1, '</s>': -0.7 },
};
const vocab = ['the','a','an','cat','dog','ant','eel','sat','ran','</s>'];

const best = beamSearch(tokens => bigrams[tokens.at(-1)] || { '</s>': 0 }, '<s>', '</s>', vocab, 3, 6);
console.log('Sequence:', best.tokens.join(' '));
console.log('Score:',    best.score.toFixed(4));
JS,
            ],
            [
                'title'       => 'Skip-Gram Negative Sampling Loss',
                'difficulty'  => 'hard',
                'description' => 'Implement the Word2Vec skip-gram loss with negative sampling. For each (center, context) pair, maximize the dot product for the positive pair (log σ(v_c·v_w)) and minimize it for k random noise words (Σ log σ(-v_n·v_w)). This trains dense word embeddings.',
                'solution_code' => <<<'JS'
function sig(x) { return 1 / (1 + Math.exp(-x)); }
function dot(a, b) { return a.reduce((s, ai, i) => s + ai * b[i], 0); }

function skipGramNSLoss(centerEmb, contextEmb, negEmbs) {
    // Positive: maximize similarity → minimize -log σ(center · context)
    const posScore = dot(centerEmb, contextEmb);
    const posLoss  = -Math.log(sig(posScore) + 1e-15);

    // Negative: minimize similarity → minimize -Σ log σ(-center · neg)
    const negLoss = negEmbs.reduce((sum, neg) => sum - Math.log(sig(-dot(centerEmb, neg)) + 1e-15), 0);

    return { total: posLoss + negLoss, posLoss, negLoss };
}

// Dim-4 embeddings
const center  = [0.2, -0.1,  0.5, 0.3];
const context = [0.1,  0.4,  0.3, -0.2];  // similar → small loss
const negWords = [
    [-0.5, 0.2, -0.3, 0.8],              // dissimilar → small neg loss
    [ 0.6,-0.4,  0.1,-0.7],
];

const loss = skipGramNSLoss(center, context, negWords);
console.log('Pos loss:', loss.posLoss.toFixed(4));
console.log('Neg loss:', loss.negLoss.toFixed(4));
console.log('Total:',    loss.total.toFixed(4));

// If embeddings were identical the positive loss would be very small
const lossIdeal = skipGramNSLoss([1,0,0,0],[1,0,0,0],[[-1,0,0,0]]);
console.log('Ideal pos loss:', lossIdeal.posLoss.toFixed(4));  // ~0.31
JS,
            ],
            [
                'title'       => 'PCA via Power Iteration',
                'difficulty'  => 'hard',
                'description' => 'Find principal components without a full SVD. Power iteration converges to the dominant eigenvector of the covariance matrix. Deflate after each component (subtract the outer product) to find successive orthogonal directions. Projects high-dimensional data onto the axes of maximum variance.',
                'solution_code' => <<<'JS'
function matMul(A,B) { return A.map(row=>B[0].map((_,j)=>row.reduce((s,_,k)=>s+row[k]*B[k][j],0))); }
function transpose(M) { return M[0].map((_,j)=>M.map(r=>r[j])); }
function vecMul(M,v) { return M.map(row=>row.reduce((s,x,j)=>s+x*v[j],0)); }
function norm(v) { return Math.sqrt(v.reduce((s,x)=>s+x*x,0)); }
function normalize(v) { const n=norm(v); return v.map(x=>x/n); }

function centerData(X) {
    const means = X[0].map((_,j)=>X.reduce((s,r)=>s+r[j],0)/X.length);
    return X.map(row=>row.map((x,j)=>x-means[j]));
}

function powerIteration(M, iters=200) {
    let v = M[0].map((_,j)=>j===0?1:0);
    for (let i=0; i<iters; i++) v = normalize(vecMul(M,v));
    const eigenvalue = v.reduce((s,vi,i)=>s+vi*vecMul(M,v)[i],0);
    return { eigenvalue, pc: v };
}

function pca(X, k=2) {
    const Xc = centerData(X);
    const n = Xc.length;
    let cov = matMul(transpose(Xc), Xc).map(row=>row.map(v=>v/n));
    const components = [];
    for (let i=0; i<k; i++) {
        const {eigenvalue, pc} = powerIteration(cov);
        components.push({pc, variance: eigenvalue});
        cov = cov.map((row,r)=>row.map((c,col)=>c - eigenvalue*pc[r]*pc[col]));
    }
    const projected = Xc.map(row=>components.map(({pc})=>row.reduce((s,x,j)=>s+x*pc[j],0)));
    return {components, projected};
}

const X = [[2.5,2.4],[0.5,0.7],[2.2,2.9],[1.9,2.2],[3.1,3.0],[2.3,2.7],[2.0,1.6],[1.0,1.1],[1.5,1.6],[1.1,0.9]];
const {components, projected} = pca(X, 1);
console.log('PC1:', components[0].pc.map(v=>+v.toFixed(3)));
console.log('Variance explained:', +components[0].variance.toFixed(3));
console.log('Projections:', projected.slice(0,4).map(p=>+p[0].toFixed(3)));
JS,
            ],
            [
                'title'       => 'Expectation-Maximization (Gaussian Mixture Model)',
                'difficulty'  => 'hard',
                'description' => 'Fit a mixture of k Gaussians using EM. E-step: compute the responsibility of each component for each data point. M-step: update component weights, means, and standard deviations using the weighted responsibilities. Repeat until convergence. A soft-assignment alternative to K-Means.',
                'solution_code' => <<<'JS'
function gaussianPDF(x, mean, std) {
    return (1 / (std * Math.sqrt(2 * Math.PI))) * Math.exp(-((x-mean)**2) / (2*std**2));
}

function fitGMM(data, k=2, iters=60) {
    const min = Math.min(...data), max = Math.max(...data);
    let means   = Array.from({length:k}, (_,i) => min + (max-min)*(i+1)/(k+1));
    let stds    = new Array(k).fill((max-min)/(2*k));
    let weights = new Array(k).fill(1/k);

    for (let iter=0; iter<iters; iter++) {
        // E-step: responsibilities
        const R = data.map(x => {
            const probs = means.map((m,j) => weights[j] * gaussianPDF(x, m, stds[j]));
            const total = probs.reduce((a,b)=>a+b, 0) || 1e-15;
            return probs.map(p => p/total);
        });

        // M-step: update parameters
        for (let j=0; j<k; j++) {
            const Nj = R.reduce((s,r)=>s+r[j], 0);
            weights[j] = Nj / data.length;
            means[j]   = data.reduce((s,x,i)=>s+R[i][j]*x, 0) / Nj;
            stds[j]    = Math.sqrt(data.reduce((s,x,i)=>s+R[i][j]*(x-means[j])**2, 0) / Nj) || 1e-6;
        }
    }
    return { means: means.map(m=>+m.toFixed(2)), stds: stds.map(s=>+s.toFixed(2)), weights: weights.map(w=>+w.toFixed(2)) };
}

// Two well-separated clusters
const c1 = Array.from({length:40}, (_,i) => 2 + (i-20)*0.1);
const c2 = Array.from({length:60}, (_,i) => 8 + (i-30)*0.15);
const model = fitGMM([...c1,...c2], 2);
console.log('Means:',   model.means);    // ≈ [2.0, 8.0]
console.log('Stds:',    model.stds);     // ≈ [0.72, 1.07]
console.log('Weights:', model.weights);  // ≈ [0.4, 0.6]
JS,
            ],
        ];
    }
}
