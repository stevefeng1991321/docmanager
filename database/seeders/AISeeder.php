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
        $this->command->info('Seeded ' . count($this->problems()) . ' AI problems (401–530).');
    }

    private function problems(): array
    {
        return array_merge(
            $this->coreProblems(),
            $this->beginnerProblems(),
            $this->intermediateProblems(),
            $this->advancedProblems(),
        );
    }

    // ─── CORE (401–430) ──────────────────────────────────────────────────────

    private function coreProblems(): array
    {
        return [
            [
                'title' => 'Sigmoid Activation Function', 'difficulty' => 'easy',
                'description' => 'Implement σ(x) = 1/(1+e^−x) and its derivative σ\'(x) = σ(x)(1−σ(x)). Sigmoid squashes any real to (0,1) and is used in binary classification output layers.',
                'solution_code' => <<<'JS'
function sigmoid(x) { return 1 / (1 + Math.exp(-x)); }
function sigmoidD(x) { const s = sigmoid(x); return s * (1 - s); }

console.log(sigmoid(0).toFixed(4));    // 0.5000
console.log(sigmoid(2).toFixed(4));    // 0.8808
console.log(sigmoid(-2).toFixed(4));   // 0.1192
console.log(sigmoidD(0).toFixed(4));   // 0.2500

const logits = [-3, -1, 0, 1, 3];
console.log(logits.map(x => +sigmoid(x).toFixed(4)));
// [0.0474, 0.2689, 0.5, 0.7311, 0.9526]
JS,
            ],
            [
                'title' => 'ReLU Activation Function', 'difficulty' => 'easy',
                'description' => 'Implement ReLU f(x)=max(0,x) and Leaky ReLU f(x)=x if x>0 else α·x. ReLU avoids vanishing gradients and is the default activation in hidden layers.',
                'solution_code' => <<<'JS'
function relu(x)              { return Math.max(0, x); }
function leakyRelu(x, a=0.01) { return x > 0 ? x : a * x; }
function reluD(x)             { return x > 0 ? 1 : 0; }

const vals = [-4, -1, 0, 1, 4];
console.log(vals.map(relu));                    // [0, 0, 0, 1, 4]
console.log(vals.map(x => leakyRelu(x)));       // [-0.04, -0.01, 0, 1, 4]
console.log(vals.map(reluD));                   // [0, 0, 0, 1, 1]
JS,
            ],
            [
                'title' => 'Softmax Function', 'difficulty' => 'easy',
                'description' => 'Convert raw logits into a probability distribution summing to 1. Subtract max before exponentiation for numerical stability.',
                'solution_code' => <<<'JS'
function softmax(logits) {
    const max  = Math.max(...logits);
    const exps = logits.map(x => Math.exp(x - max));
    const sum  = exps.reduce((a, b) => a + b, 0);
    return exps.map(e => e / sum);
}

console.log(softmax([2, 1, 0.5]).map(p => +p.toFixed(4)));
// [0.6364, 0.2341, 0.1295]
console.log(softmax([1000, 1001, 1002]).map(p => +p.toFixed(4)));
// [0.0900, 0.2447, 0.6652]  (numerically stable)
JS,
            ],
            [
                'title' => 'Cosine Similarity', 'difficulty' => 'easy',
                'description' => 'Compute cos(A,B)=(A·B)/(||A||×||B||). Returns [-1,1]. Widely used in NLP to compare word or sentence embeddings.',
                'solution_code' => <<<'JS'
function cosineSim(a, b) {
    const dot  = a.reduce((s, ai, i) => s + ai * b[i], 0);
    const magA = Math.sqrt(a.reduce((s, ai) => s + ai * ai, 0));
    const magB = Math.sqrt(b.reduce((s, bi) => s + bi * bi, 0));
    return magA && magB ? dot / (magA * magB) : 0;
}

console.log(+cosineSim([1,2,3],[1,2,3]).toFixed(4));       // 1
console.log(+cosineSim([1,0],[0,1]).toFixed(4));            // 0
console.log(+cosineSim([1,2],[-1,-2]).toFixed(4));          // -1
console.log(+cosineSim([1,3,2,5],[1,2,3,4]).toFixed(4));   // 0.9818
JS,
            ],
            [
                'title' => 'Loss Functions: MSE and Binary Cross-Entropy', 'difficulty' => 'easy',
                'description' => 'MSE=(1/n)Σ(y−ŷ)² for regression. BCE=-(1/n)Σ[y·log(ŷ)+(1−y)·log(1−ŷ)] for binary classification. Clip predictions to avoid log(0).',
                'solution_code' => <<<'JS'
function mse(actual, pred) {
    return actual.reduce((s,y,i)=>s+(y-pred[i])**2,0) / actual.length;
}
function bce(actual, pred) {
    const e=1e-15;
    return -actual.reduce((s,y,i)=>{
        const p=Math.min(Math.max(pred[i],e),1-e);
        return s+y*Math.log(p)+(1-y)*Math.log(1-p);
    },0) / actual.length;
}

const actual=[1,0,1,1,0], pred=[0.9,0.1,0.8,0.7,0.2];
console.log(+mse(actual,pred).toFixed(4));  // 0.03
console.log(+bce(actual,pred).toFixed(4));  // 0.1965
JS,
            ],
            [
                'title' => 'Euclidean and Manhattan Distance', 'difficulty' => 'easy',
                'description' => 'Implement L2 (straight-line) and L1 (city-block) distance. Core metrics in KNN, K-Means, and most distance-based algorithms.',
                'solution_code' => <<<'JS'
function euclidean(a, b) {
    return Math.sqrt(a.reduce((s,ai,i)=>(s+(ai-b[i])**2),0));
}
function manhattan(a, b) {
    return a.reduce((s,ai,i)=>s+Math.abs(ai-b[i]),0);
}

console.log(euclidean([0,0],[3,4]));      // 5
console.log(manhattan([0,0],[3,4]));      // 7
console.log(euclidean([1,2,3],[4,6,3])); // 5
console.log(manhattan([1,2,3],[4,6,3])); // 7
JS,
            ],
            [
                'title' => 'One-Hot Encoding', 'difficulty' => 'easy',
                'description' => 'Convert categorical labels into binary vectors. Each label maps to a vector with a 1 at its class index and 0 elsewhere.',
                'solution_code' => <<<'JS'
function oneHot(labels) {
    const classes = [...new Set(labels)].sort();
    const idx = Object.fromEntries(classes.map((c,i)=>[c,i]));
    return {
        classes,
        encoded: labels.map(l => { const v=new Array(classes.length).fill(0); v[idx[l]]=1; return v; }),
    };
}

const {classes, encoded} = oneHot(['cat','dog','cat','bird','dog']);
console.log('Classes:', classes);     // ['bird','cat','dog']
console.log('cat  →', encoded[0]);    // [0,1,0]
console.log('bird →', encoded[3]);    // [1,0,0]
JS,
            ],
            [
                'title' => 'Feature Scaling: Min-Max and Z-Score', 'difficulty' => 'easy',
                'description' => 'Min-Max maps values to [0,1]. Z-Score standardizes to mean=0, std=1. Prevents large-range features from dominating distance-based models.',
                'solution_code' => <<<'JS'
function minMax(data) {
    const min=Math.min(...data), max=Math.max(...data), r=max-min||1;
    return data.map(x=>+((x-min)/r).toFixed(4));
}
function zScore(data) {
    const n=data.length, mean=data.reduce((a,b)=>a+b,0)/n;
    const std=Math.sqrt(data.reduce((s,x)=>s+(x-mean)**2,0)/n)||1;
    return data.map(x=>+((x-mean)/std).toFixed(4));
}

const f=[100,200,300,400,500];
console.log(minMax(f));   // [0, 0.25, 0.5, 0.75, 1]
console.log(zScore(f));   // [-1.4142,-0.7071,0,0.7071,1.4142]
JS,
            ],
            [
                'title' => 'Vector and Matrix Operations', 'difficulty' => 'easy',
                'description' => 'Core linear algebra for ML: dot product, vector addition, scalar multiply, 2D matrix multiply — the building blocks of every neural network layer.',
                'solution_code' => <<<'JS'
const dot    = (a,b) => a.reduce((s,ai,i)=>s+ai*b[i],0);
const vecAdd = (a,b) => a.map((ai,i)=>ai+b[i]);
const scale  = (a,k) => a.map(ai=>ai*k);
const norm   = a    => Math.sqrt(dot(a,a));
const matMul = (A,B) => A.map(row=>B[0].map((_,j)=>row.reduce((s,_,k)=>s+row[k]*B[k][j],0)));

console.log(dot([1,2,3],[4,5,6]));       // 32
console.log(norm([3,4]));                 // 5
console.log(vecAdd([1,2],[3,4]));         // [4,6]
console.log(matMul([[1,2],[3,4]],[[5,6],[7,8]])); // [[19,22],[43,50]]
JS,
            ],
            [
                'title' => 'Categorical Cross-Entropy Loss', 'difficulty' => 'easy',
                'description' => 'Multi-class loss: L=-(1/n)Σlog(ŷ[true_class]). Takes class indices as labels and a softmax probability matrix as predictions.',
                'solution_code' => <<<'JS'
function catCE(labels, preds) {
    const e=1e-15;
    return -labels.reduce((s,l,i)=>s+Math.log(Math.min(Math.max(preds[i][l],e),1-e)),0)/labels.length;
}

const labels=[0,1,2,1];
const preds=[[0.9,0.05,0.05],[0.1,0.8,0.1],[0.1,0.2,0.7],[0.3,0.4,0.3]];
console.log(+catCE(labels,preds).toFixed(4));  // ~0.4255

const perfect=[[1,0,0],[0,1,0],[0,0,1],[0,1,0]];
console.log(+catCE(labels,perfect).toFixed(6)); // ~0.0000
JS,
            ],
            [
                'title' => 'K-Nearest Neighbors (KNN) Classifier', 'difficulty' => 'medium',
                'description' => 'For each query find the k closest training points using Euclidean distance and return the majority class. Non-parametric lazy learner — no training phase.',
                'solution_code' => <<<'JS'
function knn(train, labels, query, k=3) {
    return train
        .map((p,i)=>({d:Math.sqrt(p.reduce((s,x,j)=>s+(x-query[j])**2,0)),l:labels[i]}))
        .sort((a,b)=>a.d-b.d).slice(0,k)
        .reduce((votes,{l})=>(votes[l]=(votes[l]||0)+1,votes),{});
}
function predict(train,labels,q,k){
    const v=knn(train,labels,q,k);
    return Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];
}

const pts=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const lbl=['A','A','A','B','B','B'];
console.log(predict(pts,lbl,[2,2],3));    // A
console.log(predict(pts,lbl,[5,4],3));    // B
JS,
            ],
            [
                'title' => 'Simple Linear Regression', 'difficulty' => 'medium',
                'description' => 'Closed-form OLS solution: compute slope m and intercept b that minimize Σ(y−ŷ)². Also compute R² goodness-of-fit.',
                'solution_code' => <<<'JS'
function linReg(xs, ys) {
    const n=xs.length, sx=xs.reduce((a,b)=>a+b,0), sy=ys.reduce((a,b)=>a+b,0);
    const sxy=xs.reduce((s,x,i)=>s+x*ys[i],0), sx2=xs.reduce((s,x)=>s+x*x,0);
    const m=(n*sxy-sx*sy)/(n*sx2-sx**2), b=(sy-m*sx)/n;
    const my=sy/n, ssTot=ys.reduce((s,y)=>s+(y-my)**2,0);
    const ssRes=xs.reduce((s,x,i)=>s+(ys[i]-(m*x+b))**2,0);
    return {m:+m.toFixed(4),b:+b.toFixed(4),r2:+(1-ssRes/ssTot).toFixed(4),p:x=>m*x+b};
}

const model=linReg([600,800,1000,1200,1400],[150,200,250,300,350]);
console.log('slope:',model.m,'intercept:',model.b,'R²:',model.r2);
console.log('predict 1100:',model.p(1100).toFixed(1));  // 212.5
JS,
            ],
            [
                'title' => 'Gradient Descent', 'difficulty' => 'medium',
                'description' => 'Iteratively move parameters in the direction of the negative gradient. Applied to minimize f(x)=(x−3)², whose gradient is 2(x−3) and minimum is x=3.',
                'solution_code' => <<<'JS'
function gd(gradFn, x0, lr=0.1, iters=50) {
    let x=x0;
    for(let i=0;i<iters;i++) x-=lr*gradFn(x);
    return +x.toFixed(6);
}

console.log(gd(x=>2*(x-3), 10));   // ≈ 3.000000
console.log(gd(x=>4*x**3, 2, 0.01, 200));  // ≈ 0 (min of x^4)

// Show convergence
let x=10;
[1,10,25,50].forEach(iter=>{
    while(iter-->0) x-=0.1*2*(x-3);
    console.log(`x≈${x.toFixed(4)}`);
});
JS,
            ],
            [
                'title' => 'Perceptron (Single Neuron Classifier)', 'difficulty' => 'medium',
                'description' => 'Perceptron update rule: w+=lr·(y−ŷ)·x. Converges on linearly separable data. Conceptual foundation of all neural networks.',
                'solution_code' => <<<'JS'
function perceptron(data, labels, lr=0.1, epochs=100) {
    let w=new Array(data[0].length).fill(0), b=0;
    const pred=x=>x.reduce((s,xi,i)=>s+xi*w[i],b)>=0?1:0;
    for(let e=0;e<epochs;e++){
        let errs=0;
        data.forEach((x,j)=>{
            const err=labels[j]-pred(x);
            if(err){w=w.map((wi,i)=>wi+lr*err*x[i]);b+=lr*err;errs++;}
        });
        if(!errs){console.log(`Converged at epoch ${e+1}`);break;}
    }
    return pred;
}

const data=[[0,0],[0,1],[1,0],[1,1]], labels=[0,0,0,1];
const p=perceptron(data,labels);
data.forEach((x,i)=>console.log(`${x}→${p(x)} (exp ${labels[i]})`));
JS,
            ],
            [
                'title' => 'Feedforward Neural Network (Forward Pass)', 'difficulty' => 'medium',
                'description' => 'Forward pass: z1=W1·x+b1, a1=ReLU(z1), z2=W2·a1+b2, out=Sigmoid(z2). The core computation every neural network inference step performs.',
                'solution_code' => <<<'JS'
const mv=(W,v)=>W.map(r=>r.reduce((s,w,i)=>s+w*v[i],0));
const add=(z,b)=>z.map((v,i)=>v+b[i]);
const relu=z=>z.map(x=>Math.max(0,x));
const sig=z=>z.map(x=>1/(1+Math.exp(-x)));

function fwd(x,W1,b1,W2,b2){
    const a1=relu(add(mv(W1,x),b1));
    return sig(add(mv(W2,a1),b2));
}

const W1=[[0.5,-0.2],[0.1,0.8],[-0.3,0.6]],b1=[0.1,-0.1,0.2];
const W2=[[0.4,0.7,-0.5]],b2=[0.1];
console.log(fwd([1.0,0.5],W1,b1,W2,b2).map(v=>+v.toFixed(4)));
JS,
            ],
            [
                'title' => 'K-Means Clustering', 'difficulty' => 'medium',
                'description' => 'Assign each point to the nearest centroid, then recompute centroids as cluster means. Repeat until labels stabilize.',
                'solution_code' => <<<'JS'
function kMeans(data,k,maxI=100){
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    let c=data.slice(0,k).map(p=>[...p]);
    let labels=data.map(()=>0);
    for(let i=0;i<maxI;i++){
        const next=data.map(p=>c.reduce((b,ci,j)=>d(p,ci)<d(p,c[b])?j:b,0));
        c=c.map((_,j)=>{
            const cl=data.filter((_,i)=>next[i]===j);
            return cl.length?cl[0].map((_,d)=>cl.reduce((s,p)=>s+p[d],0)/cl.length):c[j];
        });
        if(JSON.stringify(next)===JSON.stringify(labels)){break;}
        labels=next;
    }
    return{centroids:c.map(ci=>ci.map(v=>+v.toFixed(2))),labels};
}

const data=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const {centroids,labels}=kMeans(data,2);
console.log('Centroids:',centroids);
console.log('Labels:',labels);
JS,
            ],
            [
                'title' => 'Confusion Matrix Metrics', 'difficulty' => 'medium',
                'description' => 'Compute TP/FP/FN/TN and derive Precision, Recall, F1, Accuracy. More informative than raw accuracy on imbalanced datasets.',
                'solution_code' => <<<'JS'
function metrics(actual,pred){
    let tp=0,fp=0,fn=0,tn=0;
    actual.forEach((y,i)=>{
        if(y&&pred[i])tp++;else if(!y&&pred[i])fp++;
        else if(y&&!pred[i])fn++;else tn++;
    });
    const pr=tp/(tp+fp)||0, re=tp/(tp+fn)||0;
    return{tp,fp,fn,tn,precision:+pr.toFixed(4),recall:+re.toFixed(4),
        f1:+(2*pr*re/(pr+re)||0).toFixed(4),accuracy:+((tp+tn)/actual.length).toFixed(4)};
}

const a=[1,1,0,1,0,1,0,0,1,0], p=[1,0,0,1,0,1,1,0,1,0];
console.log(metrics(a,p));
// {tp:5,fp:1,fn:1,tn:3,precision:0.8333,recall:0.8333,f1:0.8333,accuracy:0.8}
JS,
            ],
            [
                'title' => 'Gaussian Naive Bayes', 'difficulty' => 'medium',
                'description' => 'Compute per-class Gaussian distributions for each feature, classify by picking the class with highest log-posterior: log P(class) + Σ log P(feat|class).',
                'solution_code' => <<<'JS'
function gnb(X,y){
    const classes=[...new Set(y)],n=y.length;
    const priors={},stats={};
    for(const c of classes){
        const idx=y.reduce((a,l,i)=>l===c?[...a,i]:a,[]);
        priors[c]=idx.length/n;
        stats[c]=X[0].map((_,j)=>{
            const v=idx.map(i=>X[i][j]);
            const m=v.reduce((a,b)=>a+b,0)/v.length;
            const vr=v.reduce((s,x)=>s+(x-m)**2,0)/v.length+1e-9;
            return{m,vr};
        });
    }
    return x=>classes.reduce((best,c)=>{
        let lp=Math.log(priors[c]);
        stats[c].forEach(({m,vr},j)=>lp+=-(x[j]-m)**2/(2*vr)-0.5*Math.log(2*Math.PI*vr));
        return lp>best.lp?{c,lp}:best;
    },{c:null,lp:-Infinity}).c;
}

const X=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]],y=[0,0,0,1,1,1];
const predict=gnb(X,y);
console.log(predict([1.5,1.5]));  // 0
console.log(predict([5.5,5.5]));  // 1
JS,
            ],
            [
                'title' => 'Logistic Regression', 'difficulty' => 'medium',
                'description' => 'Train logistic regression with batch gradient descent. Output P(y=1|x)=σ(w·x+b). Gradient: (ŷ−y)·x averaged over the batch.',
                'solution_code' => <<<'JS'
function logReg(X,y,lr=0.3,epochs=500){
    const sig=x=>1/(1+Math.exp(-x));
    let w=new Array(X[0].length).fill(0),b=0;
    const pred=x=>sig(x.reduce((s,xi,i)=>s+xi*w[i],b));
    for(let e=0;e<epochs;e++){
        const dw=new Array(w.length).fill(0);let db=0;
        X.forEach((x,j)=>{const err=pred(x)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi-lr*dw[i]/X.length);b-=lr*db/X.length;
    }
    return x=>pred(x)>=0.5?1:0;
}

const X=[[0.5,1.5],[1.5,0.5],[1,1],[5,4.5],[4.5,5],[5,5.5]],y=[0,0,0,1,1,1];
const clf=logReg(X,y);
console.log(clf([1,1]));    // 0
console.log(clf([5,5]));    // 1
JS,
            ],
            [
                'title' => 'Batch Normalization Forward Pass', 'difficulty' => 'medium',
                'description' => 'Normalize each feature across the batch to mean=0, std=1, then scale and shift with learnable γ and β. Stabilizes training and allows higher learning rates.',
                'solution_code' => <<<'JS'
function batchNorm(X,gamma,beta,eps=1e-8){
    const B=X.length,F=X[0].length;
    const means=Array.from({length:F},(_,j)=>X.reduce((s,r)=>s+r[j],0)/B);
    const vars =Array.from({length:F},(_,j)=>X.reduce((s,r)=>s+(r[j]-means[j])**2,0)/B);
    const Xn=X.map(r=>r.map((x,j)=>(x-means[j])/Math.sqrt(vars[j]+eps)));
    return{out:Xn.map(r=>r.map((xn,j)=>gamma[j]*xn+beta[j])),means,vars};
}

const X=[[2,3,5],[1,7,3],[3,5,1],[2,1,7]];
const {out,means,vars}=batchNorm(X,[1,1,1],[0,0,0]);
console.log('means:',means.map(v=>+v.toFixed(1)));  // [2,4,4]
console.log('vars:',vars.map(v=>+v.toFixed(1)));    // [0.5,5,4]
console.log('row0 norm:',out[0].map(v=>+v.toFixed(3)));
JS,
            ],
            [
                'title' => 'Backpropagation (XOR Network)', 'difficulty' => 'hard',
                'description' => 'Train a 2-hidden-unit network on XOR using backprop. Propagate gradients through sigmoid output → W2/b2 → hidden → W1/b1. XOR requires at least one hidden layer.',
                'solution_code' => <<<'JS'
const sig=x=>1/(1+Math.exp(-x)), sigD=x=>{const s=sig(x);return s*(1-s)};
let W1=[[0.5,-0.3],[0.2,0.8]],b1=[0.1,-0.1],W2=[[0.4,0.7]],b2=[0],lr=0.5;
const data=[[0,0],[0,1],[1,0],[1,1]],lbls=[0,1,1,0];

for(let e=0;e<5000;e++) data.forEach((x,d)=>{
    const z1=W1.map((r,i)=>r.reduce((s,w,j)=>s+w*x[j],b1[i])),a1=z1.map(sig);
    const z2=[W2[0].reduce((s,w,j)=>s+w*a1[j],b2[0])],out=sig(z2[0]);
    const dZ2=(out-lbls[d])*sigD(z2[0]),dW2=a1.map(a=>dZ2*a);
    const dA1=W2[0].map(w=>w*dZ2),dZ1=dA1.map((v,i)=>v*sigD(z1[i]));
    W2[0]=W2[0].map((w,j)=>w-lr*dW2[j]);b2[0]-=lr*dZ2;
    W1=W1.map((r,i)=>r.map((w,j)=>w-lr*dZ1[i]*x[j]));b1=b1.map((v,i)=>v-lr*dZ1[i]);
});

const prd=x=>{const a1=W1.map((r,i)=>sig(r.reduce((s,w,j)=>s+w*x[j],b1[i])));return sig(W2[0].reduce((s,w,j)=>s+w*a1[j],b2[0]));};
data.forEach(x=>console.log(`[${x}]→${prd(x).toFixed(4)}`));
// [0,0]→~0.03  [0,1]→~0.97  [1,0]→~0.97  [1,1]→~0.03
JS,
            ],
            [
                'title' => 'Convolutional Layer Forward Pass', 'difficulty' => 'hard',
                'description' => 'Slide a kernel over a 2D input with configurable stride and zero-padding, computing element-wise dot products at each position. Core CNN computation for feature extraction.',
                'solution_code' => <<<'JS'
function conv2d(input,kernel,stride=1,pad=0){
    const H=input.length,W=input[0].length,kH=kernel.length,kW=kernel[0].length;
    const p=Array.from({length:H+2*pad},(_,r)=>Array.from({length:W+2*pad},(_,c)=>{
        const rr=r-pad,cc=c-pad;return(rr>=0&&rr<H&&cc>=0&&cc<W)?input[rr][cc]:0;
    }));
    const oH=Math.floor((H+2*pad-kH)/stride)+1,oW=Math.floor((W+2*pad-kW)/stride)+1;
    const out=Array.from({length:oH},()=>new Array(oW).fill(0));
    for(let r=0;r<oH;r++)for(let c=0;c<oW;c++)
        for(let kr=0;kr<kH;kr++)for(let kc=0;kc<kW;kc++)
            out[r][c]+=p[r*stride+kr][c*stride+kc]*kernel[kr][kc];
    return out;
}

const img=[[1,2,3,0],[0,1,2,3],[3,0,1,2],[2,3,0,1]];
const sobelX=[[-1,0,1],[-2,0,2],[-1,0,1]];
conv2d(img,sobelX).forEach(r=>console.log(r));
console.log('padded size:',conv2d(img,sobelX,1,1).length,'x',conv2d(img,sobelX,1,1)[0].length); // 4x4
JS,
            ],
            [
                'title' => 'LSTM Cell Forward Pass', 'difficulty' => 'hard',
                'description' => 'One LSTM time step: forget gate f, input gate i, candidate cell g, output gate o. Cell state c accumulates long-term memory; hidden state h carries short-term context.',
                'solution_code' => <<<'JS'
const sig=x=>1/(1+Math.exp(-x));
function lstm(x,h,c,{Wf,Wi,Wg,Wo,Uf,Ui,Ug,Uo,bf,bi,bg,bo}){
    const f=sig(Wf*x+Uf*h+bf),i=sig(Wi*x+Ui*h+bi);
    const g=Math.tanh(Wg*x+Ug*h+bg),o=sig(Wo*x+Uo*h+bo);
    const nc=f*c+i*g,nh=o*Math.tanh(nc);
    return{h:nh,c:nc};
}

const p={Wf:.5,Wi:.6,Wg:.4,Wo:.7,Uf:.3,Ui:.2,Ug:.5,Uo:.4,bf:-.1,bi:0,bg:.1,bo:-.2};
let h=0,c=0;
for(const x of[1,.5,-.3,.8]){
    ({h,c}=lstm(x,h,c,p));
    console.log(`x=${x}: h=${h.toFixed(4)}, c=${c.toFixed(4)}`);
}
JS,
            ],
            [
                'title' => 'Scaled Dot-Product Attention (Transformer)', 'difficulty' => 'hard',
                'description' => 'Attention(Q,K,V)=softmax(Q·K^T/√d_k)·V. Dividing by √d_k prevents gradients from vanishing when dot products grow large in high dimensions.',
                'solution_code' => <<<'JS'
const sfmx=r=>{const m=Math.max(...r),e=r.map(x=>Math.exp(x-m)),s=e.reduce((a,b)=>a+b);return e.map(x=>x/s);};
const mm=(A,B)=>A.map(r=>B[0].map((_,j)=>r.reduce((s,_,k)=>s+r[k]*B[k][j],0)));
const T=M=>M[0].map((_,j)=>M.map(r=>r[j]));

function attn(Q,K,V){
    const dk=Q[0].length;
    const scores=mm(Q,T(K)).map(r=>r.map(s=>s/Math.sqrt(dk)));
    const w=scores.map(sfmx);
    return{out:mm(w,V),weights:w};
}

const Q=[[1,0,1,0],[0,1,0,1],[1,1,0,0]],K=[[1,0,1,0],[0,1,0,1],[1,0,0,1]],V=[[1,0],[0,1],[1,1]];
const{out,weights}=attn(Q,K,V);
console.log('weights:'); weights.forEach(r=>console.log(r.map(v=>+v.toFixed(3))));
console.log('output:');  out.forEach(r=>console.log(r.map(v=>+v.toFixed(4))));
JS,
            ],
            [
                'title' => 'Adam Optimizer', 'difficulty' => 'hard',
                'description' => 'Maintain exponential moving averages of gradients (m) and squared gradients (v), apply bias correction, update: lr·m̂/(√v̂+ε). Combines momentum with adaptive per-parameter learning rates.',
                'solution_code' => <<<'JS'
function adam(params,grads,m,v,t,lr=.001,b1=.9,b2=.999,eps=1e-8){
    const np={},nm={},nv={};
    for(const k in params){
        nm[k]=b1*m[k]+(1-b1)*grads[k];
        nv[k]=b2*v[k]+(1-b2)*grads[k]**2;
        np[k]=params[k]-lr*(nm[k]/(1-b1**t))/(Math.sqrt(nv[k]/(1-b2**t))+eps);
    }
    return{params:np,m:nm,v:nv};
}

let s={params:{w:10},m:{w:0},v:{w:0}};
for(let t=1;t<=20;t++){
    s=adam(s.params,{w:2*s.params.w},s.m,s.v,t,.1);
    if([1,5,10,20].includes(t))console.log(`step ${t}: w=${s.params.w.toFixed(4)}`);
}
// converges toward 0
JS,
            ],
            [
                'title' => 'Dropout (Inverted)', 'difficulty' => 'hard',
                'description' => 'During training zero neurons with probability p and scale survivors by 1/(1−p) to preserve expected values. At inference pass through unchanged. Prevents co-adaptation of neurons.',
                'solution_code' => <<<'JS'
function dropout(a,rate,train=true){
    if(!train)return[...a];
    const keep=1-rate;
    const mask=a.map(()=>Math.random()<keep?1:0);
    return{out:a.map((x,i)=>x*mask[i]/keep),mask,dropped:mask.filter(m=>!m).length};
}

const neurons=[.5,.8,.3,.9,.6,.7,.4,.2];

// Deterministic demo mask
function dropoutFixed(a,mask,rate){
    return a.map((x,i)=>x*mask[i]/(1-rate));
}
const mask=[1,0,1,1,0,1,0,1];
const out=dropoutFixed(neurons,mask,.5);
console.log('input: ',neurons);
console.log('mask:  ',mask);
console.log('output:',out.map(v=>+v.toFixed(2)));
console.log('inference:',dropout(neurons,.5,false));
JS,
            ],
            [
                'title' => 'Beam Search Decoder', 'difficulty' => 'hard',
                'description' => 'At each step expand every beam by all vocab tokens and keep top-k by cumulative log-probability. Wider beams trade compute for output quality vs greedy (k=1).',
                'solution_code' => <<<'JS'
function beamSearch(logProbFn,start,end,vocab,k=3,maxLen=8){
    let beams=[{tokens:[start],score:0}],done=[];
    for(let s=0;s<maxLen;s++){
        const cands=[];
        for(const b of beams){
            if(b.tokens.at(-1)===end){done.push(b);continue;}
            const lp=logProbFn(b.tokens);
            for(const t of vocab) cands.push({tokens:[...b.tokens,t],score:b.score+(lp[t]??-Infinity)});
        }
        cands.sort((a,b)=>b.score-a.score);
        beams=cands.slice(0,k);
        if(beams.every(b=>b.tokens.at(-1)===end))break;
    }
    return[...beams,...done].sort((a,b)=>b.score-a.score)[0];
}

const lp={'<s>':{the:-.5,a:-.9},the:{cat:-.6,dog:-.8,'</s>':-2},a:{cat:-.4,dog:-1.2,'</s>':-2},cat:{'</s>':-0.8,sat:-.5},dog:{'</s>':-0.7}};
const best=beamSearch(t=>lp[t.at(-1)]||{},'<s>','</s>',['the','a','cat','dog','</s>'],3,5);
console.log(best.tokens.join(' '),best.score.toFixed(3));
JS,
            ],
            [
                'title' => 'Skip-Gram Negative Sampling Loss', 'difficulty' => 'hard',
                'description' => 'Maximize log σ(center·context) for positive pairs and log σ(−center·neg) for noise words. Trains dense word embeddings without computing full softmax over vocabulary.',
                'solution_code' => <<<'JS'
const sig=x=>1/(1+Math.exp(-x));
const dot=(a,b)=>a.reduce((s,x,i)=>s+x*b[i],0);

function sgNSLoss(center,context,negs){
    const pos=-Math.log(sig(dot(center,context))+1e-15);
    const neg=negs.reduce((s,n)=>s-Math.log(sig(-dot(center,n))+1e-15),0);
    return{total:pos+neg,pos,neg};
}

const c=[.2,-.1,.5,.3], ctx=[.1,.4,.3,-.2];
const negs=[[-0.5,.2,-.3,.8],[.6,-.4,.1,-.7]];
const l=sgNSLoss(c,ctx,negs);
console.log('pos loss:',l.pos.toFixed(4));
console.log('neg loss:',l.neg.toFixed(4));
console.log('total:',l.total.toFixed(4));
JS,
            ],
            [
                'title' => 'PCA via Power Iteration', 'difficulty' => 'hard',
                'description' => 'Find principal components without full SVD. Power iteration converges to the dominant eigenvector of the covariance matrix. Deflate to find successive orthogonal directions.',
                'solution_code' => <<<'JS'
const mm=(A,B)=>A.map(r=>B[0].map((_,j)=>r.reduce((s,_,k)=>s+r[k]*B[k][j],0)));
const T=M=>M[0].map((_,j)=>M.map(r=>r[j]));
const mvMul=(M,v)=>M.map(r=>r.reduce((s,x,j)=>s+x*v[j],0));
const nrm=v=>Math.sqrt(v.reduce((s,x)=>s+x*x,0));
const nor=v=>{const n=nrm(v);return v.map(x=>x/n);};
const center=X=>{const m=X[0].map((_,j)=>X.reduce((s,r)=>s+r[j],0)/X.length);return X.map(r=>r.map((x,j)=>x-m[j]));};

function pca(X,k=1){
    const Xc=center(X),n=Xc.length;
    let cov=mm(T(Xc),Xc).map(r=>r.map(v=>v/n)),comps=[];
    for(let i=0;i<k;i++){
        let v=cov[0].map((_,j)=>j===0?1:0);
        for(let it=0;it<200;it++)v=nor(mvMul(cov,v));
        const ev=v.reduce((s,vi,i)=>s+vi*mvMul(cov,v)[i],0);
        comps.push({v,ev});
        cov=cov.map((r,ri)=>r.map((c,ci)=>c-ev*v[ri]*v[ci]));
    }
    return comps;
}

const X=[[2.5,2.4],[0.5,.7],[2.2,2.9],[1.9,2.2],[3.1,3],[2.3,2.7],[2,1.6],[1,1.1],[1.5,1.6],[1.1,.9]];
const [pc1]=pca(X,1);
console.log('PC1:',pc1.v.map(v=>+v.toFixed(3)));
console.log('variance:',+pc1.ev.toFixed(3));
JS,
            ],
            [
                'title' => 'Expectation-Maximization (Gaussian Mixture Model)', 'difficulty' => 'hard',
                'description' => 'E-step: compute responsibility of each Gaussian for each point. M-step: update weights, means, stds using weighted responsibilities. Soft-assignment alternative to K-Means.',
                'solution_code' => <<<'JS'
function gpdf(x,m,s){return Math.exp(-((x-m)**2)/(2*s**2))/(s*Math.sqrt(2*Math.PI));}

function gmm(data,k=2,iters=60){
    const mn=Math.min(...data),mx=Math.max(...data);
    let means=Array.from({length:k},(_,i)=>mn+(mx-mn)*(i+1)/(k+1));
    let stds=new Array(k).fill((mx-mn)/(2*k)),ws=new Array(k).fill(1/k);
    for(let it=0;it<iters;it++){
        const R=data.map(x=>{const p=means.map((m,j)=>ws[j]*gpdf(x,m,stds[j]));const t=p.reduce((a,b)=>a+b,0)||1e-15;return p.map(v=>v/t);});
        for(let j=0;j<k;j++){
            const Nj=R.reduce((s,r)=>s+r[j],0);
            ws[j]=Nj/data.length;
            means[j]=data.reduce((s,x,i)=>s+R[i][j]*x,0)/Nj;
            stds[j]=Math.sqrt(data.reduce((s,x,i)=>s+R[i][j]*(x-means[j])**2,0)/Nj)||1e-6;
        }
    }
    return{means:means.map(m=>+m.toFixed(2)),stds:stds.map(s=>+s.toFixed(2)),ws:ws.map(w=>+w.toFixed(2))};
}

const c1=Array.from({length:40},(_,i)=>2+(i-20)*.1),c2=Array.from({length:60},(_,i)=>8+(i-30)*.15);
const r=gmm([...c1,...c2],2);
console.log('means:',r.means);  // ≈[2.0,8.0]
console.log('stds:',r.stds);
console.log('weights:',r.ws);   // ≈[0.4,0.6]
JS,
            ],
        ];
    }

    // ─── BEGINNER / EASY (431–460) ────────────────────────────────────────────

    private function beginnerProblems(): array
    {
        return [
            [
                'title' => 'Parse CSV String', 'difficulty' => 'easy',
                'description' => 'Parse a CSV-formatted string into an array of objects using the first row as headers. Equivalent to pandas read_csv() for in-memory string data.',
                'solution_code' => <<<'JS'
function parseCSV(csv) {
    const lines = csv.trim().split('\n');
    const headers = lines[0].split(',').map(h => h.trim());
    return lines.slice(1).map(line => {
        const vals = line.split(',');
        return Object.fromEntries(headers.map((h, i) => [h, vals[i]?.trim()]));
    });
}

const csv = `name,age,score\nAlice,25,88\nBob,30,92\nCarol,22,79\nDave,28,85`;
const df = parseCSV(csv);
console.log(df.length, 'rows');          // 4
console.log(df[0]);                       // {name:'Alice',age:'25',score:'88'}
console.log(df.map(r => r.name));        // ['Alice','Bob','Carol','Dave']
JS,
            ],
            [
                'title' => 'Dataset Head (First N Rows)', 'difficulty' => 'easy',
                'description' => 'Return the first n rows of a dataset. Equivalent to df.head(n) in pandas. Essential for quickly inspecting large datasets.',
                'solution_code' => <<<'JS'
function head(data, n = 10) { return data.slice(0, n); }
function tail(data, n = 10) { return data.slice(-n); }

const data = Array.from({length: 50}, (_, i) => ({id: i+1, value: (i+1)*3, label: i%2===0?'A':'B'}));

console.log('head(5):');
head(data, 5).forEach(r => console.log(r));

console.log('\ntail(3):');
tail(data, 3).forEach(r => console.log(r));

console.log('\nShape:', data.length, 'rows ×', Object.keys(data[0]).length, 'cols');
JS,
            ],
            [
                'title' => 'Find Missing Values in Dataset', 'difficulty' => 'easy',
                'description' => 'Count null, empty, and "NA" values per column. Equivalent to df.isnull().sum() in pandas. The first step in any data cleaning pipeline.',
                'solution_code' => <<<'JS'
function isMissing(v) { return v == null || v === '' || v === 'NA' || v === 'NaN'; }

function missingCounts(data) {
    const cols = Object.keys(data[0]);
    return Object.fromEntries(cols.map(col => [col, data.filter(r => isMissing(r[col])).length]));
}

const data = [
    {name: 'Alice', age: 25,   city: 'NY'},
    {name: 'Bob',   age: null, city: 'LA'},
    {name: '',      age: 28,   city: 'NA'},
    {name: 'Dana',  age: 35,   city: null},
];
const missing = missingCounts(data);
console.log(missing);
// {name:1, age:1, city:2}
const total = Object.values(missing).reduce((a, b) => a + b, 0);
console.log(`Total missing: ${total} of ${data.length * Object.keys(data[0]).length}`);
JS,
            ],
            [
                'title' => 'Fill Missing Values with Column Mean', 'difficulty' => 'easy',
                'description' => 'Replace null values in a numeric column with the column\'s mean. Equivalent to df[col].fillna(df[col].mean()). Common imputation strategy for numeric features.',
                'solution_code' => <<<'JS'
function fillMean(data, col) {
    const nums = data.map(r => r[col]).filter(v => v != null && !isNaN(v));
    const mean = nums.reduce((a, b) => a + b, 0) / nums.length;
    return data.map(r => ({...r, [col]: r[col] == null ? +mean.toFixed(2) : r[col]}));
}

const data = [
    {id:1, score:80}, {id:2, score:null}, {id:3, score:90},
    {id:4, score:null}, {id:5, score:70},
];
console.log('Before:', data.map(r => r.score));  // [80,null,90,null,70]
const filled = fillMean(data, 'score');
console.log('After: ', filled.map(r => r.score)); // [80,80,90,80,70]  mean=80
JS,
            ],
            [
                'title' => 'Drop Rows with Null Values', 'difficulty' => 'easy',
                'description' => 'Remove rows containing any missing value. Equivalent to df.dropna(). Use when missing data is too sparse to reliably impute.',
                'solution_code' => <<<'JS'
function dropNulls(data, cols = null) {
    const check = cols ?? Object.keys(data[0]);
    return data.filter(row => check.every(col => row[col] != null && row[col] !== '' && row[col] !== 'NA'));
}

const data = [
    {a:1, b:2, c:3}, {a:null, b:3, c:4}, {a:4, b:null, c:5},
    {a:5, b:6, c:7}, {a:6, b:7, c:'NA'},
];
console.log('All cols:', dropNulls(data).length, 'rows kept');         // 2
console.log('Col a only:', dropNulls(data, ['a']).length, 'rows kept'); // 4
JS,
            ],
            [
                'title' => 'Normalize Dataset Column (Min-Max)', 'difficulty' => 'easy',
                'description' => 'Scale a numeric column to [0, 1] using min-max normalization. Equivalent to sklearn MinMaxScaler applied per column. Preserves relative distances between values.',
                'solution_code' => <<<'JS'
function normalizeCol(data, col) {
    const vals = data.map(r => r[col]);
    const min = Math.min(...vals), max = Math.max(...vals), rng = max - min || 1;
    return data.map(r => ({...r, [col]: +((r[col] - min) / rng).toFixed(4)}));
}

const data = [
    {product:'A', price:10}, {product:'B', price:40},
    {product:'C', price:70}, {product:'D', price:100},
];
const normalized = normalizeCol(data, 'price');
console.log(normalized.map(r => ({product:r.product, price:r.price})));
// prices: [0, 0.3333, 0.6667, 1]
JS,
            ],
            [
                'title' => 'Standardize Dataset Column (Z-Score)', 'difficulty' => 'easy',
                'description' => 'Center a column to mean=0 and scale to std=1. Equivalent to sklearn StandardScaler. Use when your model assumes Gaussian-distributed features.',
                'solution_code' => <<<'JS'
function standardizeCol(data, col) {
    const vals = data.map(r => r[col]);
    const n = vals.length, mean = vals.reduce((a, b) => a + b, 0) / n;
    const std = Math.sqrt(vals.reduce((s, v) => s + (v - mean) ** 2, 0) / n) || 1;
    return data.map(r => ({...r, [col]: +((r[col] - mean) / std).toFixed(4)}));
}

const data = [
    {name:'A',v:2},{name:'B',v:4},{name:'C',v:4},{name:'D',v:4},{name:'E',v:6},
];
const std = standardizeCol(data, 'v');
console.log(std.map(r => r.v));
// [-1.5811, 0.0, 0.0, 0.0, 1.5811]
console.log('mean:', std.reduce((s,r)=>s+r.v,0).toFixed(6));  // ~0
JS,
            ],
            [
                'title' => 'Label Encode Categorical Column', 'difficulty' => 'easy',
                'description' => 'Map each unique category in a column to a unique integer. Equivalent to sklearn LabelEncoder. Required before passing categorical data to most ML algorithms.',
                'solution_code' => <<<'JS'
function labelEncode(data, col) {
    const unique = [...new Set(data.map(r => r[col]))].sort();
    const mapping = Object.fromEntries(unique.map((v, i) => [v, i]));
    const encoded = data.map(r => ({...r, [`${col}_enc`]: mapping[r[col]]}));
    return {encoded, mapping, classes: unique};
}

const data = [
    {fruit:'apple',count:5}, {fruit:'banana',count:3},
    {fruit:'cherry',count:8},{fruit:'apple',count:2},
];
const {encoded, mapping} = labelEncode(data, 'fruit');
console.log('Mapping:', mapping);                     // {apple:0,banana:1,cherry:2}
console.log(encoded.map(r => r.fruit_enc));           // [0,1,2,0]
JS,
            ],
            [
                'title' => 'ASCII Bar Chart', 'difficulty' => 'easy',
                'description' => 'Render a horizontal bar chart to the console by scaling values to a fixed character width. A text-based alternative to matplotlib bar charts for quick data inspection.',
                'solution_code' => <<<'JS'
function barChart(data, labelKey, valueKey, width = 30) {
    const max = Math.max(...data.map(r => r[valueKey]));
    const line = '─'.repeat(width + 14);
    console.log(line);
    for (const row of data) {
        const bars = Math.round(row[valueKey] / max * width);
        const label = String(row[labelKey]).padEnd(10);
        const val   = String(row[valueKey]).padStart(4);
        console.log(`${label} |${'█'.repeat(bars)}${' '.repeat(width-bars)}| ${val}`);
    }
    console.log(line);
}

barChart([
    {category:'Math',    score:85},
    {category:'Science', score:92},
    {category:'English', score:78},
    {category:'History', score:65},
], 'category', 'score');
JS,
            ],
            [
                'title' => 'Mean, Median, and Mode', 'difficulty' => 'easy',
                'description' => 'Compute the three measures of central tendency. Mean = sum/n. Median = middle value of sorted array. Mode = most frequent value(s). Essential descriptive statistics.',
                'solution_code' => <<<'JS'
function stats(data) {
    const s = [...data].sort((a, b) => a - b), n = data.length;
    const mean   = data.reduce((a, b) => a + b, 0) / n;
    const median = n % 2 ? s[n>>1] : (s[n/2-1]+s[n/2])/2;
    const freq   = {}; data.forEach(v => freq[v] = (freq[v]||0)+1);
    const maxF   = Math.max(...Object.values(freq));
    const mode   = Object.entries(freq).filter(([,f])=>f===maxF).map(([v])=>+v);
    return {mean:+mean.toFixed(4), median, mode};
}

console.log(stats([4,7,13,2,7,9,7,1,4]));
// {mean:6, median:7, mode:[7]}
console.log(stats([1,2,3,4]));
// {mean:2.5, median:2.5, mode:[1,2,3,4]}
console.log(stats([5,5,5,5]));
// {mean:5, median:5, mode:[5]}
JS,
            ],
            [
                'title' => 'Serialize Array to CSV String', 'difficulty' => 'easy',
                'description' => 'Convert an array of objects into a CSV-formatted string, escaping values that contain commas or quotes. Equivalent to df.to_csv() in pandas.',
                'solution_code' => <<<'JS'
function toCSV(data) {
    if (!data.length) return '';
    const headers = Object.keys(data[0]);
    const escape = v => /[,"\n]/.test(String(v)) ? `"${String(v).replace(/"/g,'""')}"` : String(v);
    return [headers.join(','), ...data.map(r => headers.map(h => escape(r[h])).join(','))].join('\n');
}

const data = [
    {name:'Alice', age:25, bio:'Engineer, AI'},
    {name:'Bob',   age:30, bio:'Writer'},
    {name:'Carol', age:22, bio:'She said "hello"'},
];
console.log(toCSV(data));
// name,age,bio
// Alice,25,"Engineer, AI"
// Bob,30,Writer
// Carol,22,"She said ""hello"""
JS,
            ],
            [
                'title' => 'Join Two Datasets by Key', 'difficulty' => 'easy',
                'description' => 'Implement inner join (keep only matching rows) and left join (keep all left rows, null-fill unmatched). Equivalent to pandas merge() or SQL JOIN.',
                'solution_code' => <<<'JS'
function innerJoin(left, right, key) {
    return left.flatMap(l => right.filter(r => r[key]===l[key]).map(r => ({...l,...r})));
}
function leftJoin(left, right, key) {
    return left.map(l => ({...l,...(right.find(r => r[key]===l[key])||{})}));
}

const employees = [{id:1,name:'Alice'},{id:2,name:'Bob'},{id:3,name:'Carol'}];
const salaries  = [{id:1,salary:90k},{id:2,salary:80}];

// Hint: fix the 90k typo — it should be 90
const salaries2 = [{id:1,salary:90},{id:2,salary:80}];
console.log('Inner:', innerJoin(employees,salaries2,'id').map(r=>`${r.name}:${r.salary}`));
// ['Alice:90','Bob:80']
console.log('Left:', leftJoin(employees,salaries2,'id').map(r=>`${r.name}:${r.salary??'N/A'}`));
// ['Alice:90','Bob:80','Carol:N/A']
JS,
            ],
            [
                'title' => 'Filter Dataset Rows', 'difficulty' => 'easy',
                'description' => 'Filter rows using flexible condition objects. Equivalent to df.query() or boolean masking in pandas. Supports >, >=, <, ==, and string contains operators.',
                'solution_code' => <<<'JS'
function filterRows(data, conditions) {
    return data.filter(row => Object.entries(conditions).every(([col,{op,val}]) => {
        switch(op){
            case '>':  return row[col] > val;
            case '>=': return row[col] >= val;
            case '<':  return row[col] < val;
            case '==': return row[col] == val;
            case 'in': return val.includes(row[col]);
            default:   return true;
        }
    }));
}

const data = [
    {name:'Alice',age:25,dept:'Eng'}, {name:'Bob',age:32,dept:'HR'},
    {name:'Carol',age:28,dept:'Eng'}, {name:'Dave',age:19,dept:'Eng'},
];
console.log(filterRows(data,{age:{op:'>=',val:25},dept:{op:'==',val:'Eng'}}).map(r=>r.name));
// ['Alice','Carol']
console.log(filterRows(data,{dept:{op:'in',val:['HR','Eng']},age:{op:'>',val:25}}).map(r=>r.name));
// ['Bob','Carol']
JS,
            ],
            [
                'title' => 'Sort Dataset by Column', 'difficulty' => 'easy',
                'description' => 'Sort an array of objects by any column, ascending or descending, handling both numeric and string comparisons. Equivalent to df.sort_values(by=col).',
                'solution_code' => <<<'JS'
function sortBy(data, col, order = 'asc') {
    return [...data].sort((a, b) => {
        const dir = order === 'asc' ? 1 : -1;
        if (typeof a[col] === 'number') return (a[col] - b[col]) * dir;
        return String(a[col]).localeCompare(String(b[col])) * dir;
    });
}

const data = [
    {name:'Carol',score:85}, {name:'Alice',score:92},
    {name:'Bob',score:78},   {name:'Dave',score:85},
];
console.log(sortBy(data,'score','desc').map(r=>`${r.name}:${r.score}`));
// ['Alice:92','Carol:85','Dave:85','Bob:78']
console.log(sortBy(data,'name').map(r=>r.name));
// ['Alice','Bob','Carol','Dave']
JS,
            ],
            [
                'title' => 'Group Dataset by Column', 'difficulty' => 'easy',
                'description' => 'Group rows by a categorical column\'s value and collect matching rows into arrays. Equivalent to df.groupby(col). Foundation for aggregation operations.',
                'solution_code' => <<<'JS'
function groupBy(data, col) {
    return data.reduce((groups, row) => {
        const key = row[col];
        (groups[key] = groups[key] || []).push(row);
        return groups;
    }, {});
}

const data = [
    {dept:'Eng',name:'Alice',salary:90},{dept:'HR',name:'Bob',salary:70},
    {dept:'Eng',name:'Carol',salary:85},{dept:'HR',name:'Dave',salary:75},
    {dept:'Eng',name:'Eve',salary:95},
];
const grouped = groupBy(data, 'dept');
for (const [dept, members] of Object.entries(grouped)) {
    const avg = members.reduce((s,m)=>s+m.salary,0)/members.length;
    console.log(`${dept}: ${members.length} members, avg salary ${avg.toFixed(0)}`);
}
JS,
            ],
            [
                'title' => 'Count Value Frequencies', 'difficulty' => 'easy',
                'description' => 'Count how often each unique value appears in a column. Equivalent to df[col].value_counts(). Useful for understanding class distribution before training.',
                'solution_code' => <<<'JS'
function valueCounts(data, col) {
    const freq = {};
    data.forEach(r => freq[r[col]] = (freq[r[col]]||0)+1);
    return Object.entries(freq).sort((a,b)=>b[1]-a[1])
        .reduce((obj,[k,v])=>({...obj,[k]:v}),{});
}

const data = [
    {label:'cat'},{label:'dog'},{label:'cat'},{label:'bird'},
    {label:'dog'},{label:'cat'},{label:'fish'},{label:'dog'},
];
const counts = valueCounts(data, 'label');
console.log(counts);           // {cat:3, dog:3, bird:1, fish:1}
const total = data.length;
Object.entries(counts).forEach(([k,v])=>console.log(`${k}: ${(v/total*100).toFixed(1)}%`));
JS,
            ],
            [
                'title' => 'Detect Duplicate Rows', 'difficulty' => 'easy',
                'description' => 'Find rows that appear more than once in a dataset. Equivalent to df[df.duplicated()]. Duplicates can bias model training by overrepresenting certain examples.',
                'solution_code' => <<<'JS'
function findDuplicates(data) {
    const seen = new Map();
    const dupes = [];
    data.forEach((row, i) => {
        const key = JSON.stringify(row);
        if (seen.has(key)) dupes.push({row, indices: [seen.get(key), i]});
        else seen.set(key, i);
    });
    return dupes;
}

const data = [
    {name:'Alice',age:25},{name:'Bob',age:30},
    {name:'Alice',age:25},{name:'Carol',age:22},
    {name:'Bob',age:30},
];
const dupes = findDuplicates(data);
console.log(`Found ${dupes.length} duplicate(s):`);
dupes.forEach(d => console.log(`${JSON.stringify(d.row)} at rows ${d.indices}`));
JS,
            ],
            [
                'title' => 'Remove Duplicate Rows', 'difficulty' => 'easy',
                'description' => 'Keep only the first occurrence of each unique row. Equivalent to df.drop_duplicates(). Call after findDuplicates to clean the dataset.',
                'solution_code' => <<<'JS'
function dedupe(data, cols = null) {
    const seen = new Set();
    return data.filter(row => {
        const key = JSON.stringify(cols ? cols.reduce((o,c)=>({...o,[c]:row[c]}),{}) : row);
        return seen.has(key) ? false : (seen.add(key), true);
    });
}

const data = [
    {id:1,name:'Alice',score:90},{id:2,name:'Bob',score:85},
    {id:1,name:'Alice',score:90},{id:3,name:'Carol',score:92},
    {id:2,name:'Bob',score:85},
];
const clean = dedupe(data);
console.log(`${data.length} rows → ${clean.length} unique`);  // 5 → 3
console.log(clean.map(r=>r.name));  // ['Alice','Bob','Carol']
JS,
            ],
            [
                'title' => 'Label Encoder Class', 'difficulty' => 'easy',
                'description' => 'A reusable LabelEncoder that can fit on training labels, transform new labels, and inverse-transform encoded values back to original categories.',
                'solution_code' => <<<'JS'
class LabelEncoder {
    constructor() { this.classes = []; this.map = {}; }
    fit(labels) {
        this.classes = [...new Set(labels)].sort();
        this.map = Object.fromEntries(this.classes.map((c,i)=>[c,i]));
        return this;
    }
    transform(labels)  { return labels.map(l => this.map[l] ?? -1); }
    inverse(encoded)   { return encoded.map(i => this.classes[i]); }
    fitTransform(labels){ return this.fit(labels).transform(labels); }
}

const enc = new LabelEncoder();
const y_train = ['cat','dog','cat','bird','dog','fish'];
const encoded = enc.fitTransform(y_train);
console.log('Classes:', enc.classes);    // ['bird','cat','dog','fish']
console.log('Encoded:', encoded);        // [1,2,1,0,2,3]
console.log('Decoded:', enc.inverse(encoded));  // original
console.log('New:',    enc.transform(['cat','bird']));  // [1,0]
JS,
            ],
            [
                'title' => 'Train/Test Split', 'difficulty' => 'easy',
                'description' => 'Randomly shuffle and split a dataset into training and test sets. Equivalent to sklearn train_test_split(). Prevents data leakage from test set into training.',
                'solution_code' => <<<'JS'
function trainTestSplit(data, testRatio = 0.2, shuffle = true) {
    let arr = [...data];
    if (shuffle) {
        for (let i = arr.length-1; i > 0; i--) {
            const j = Math.floor(Math.random()*(i+1));
            [arr[i],arr[j]] = [arr[j],arr[i]];
        }
    }
    const split = Math.floor(arr.length * (1 - testRatio));
    return {train: arr.slice(0, split), test: arr.slice(split)};
}

const dataset = Array.from({length:100}, (_, i) => ({id:i, label:i<60?0:1}));
const {train, test} = trainTestSplit(dataset, 0.2);
console.log(`Train: ${train.length}, Test: ${test.length}`);  // 80, 20
const testLabel1 = test.filter(r=>r.label===1).length;
console.log(`Test class 1: ${testLabel1} (${(testLabel1/test.length*100).toFixed(0)}%)`);
JS,
            ],
            [
                'title' => 'Extract Features and Labels', 'difficulty' => 'easy',
                'description' => 'Split a dataset into a feature matrix X (numeric arrays) and a label vector y. Equivalent to X = df.drop(target, axis=1) and y = df[target].',
                'solution_code' => <<<'JS'
function extractXY(data, target) {
    const feats = Object.keys(data[0]).filter(k => k !== target);
    const X = data.map(r => feats.map(f => +r[f]));
    const y = data.map(r => r[target]);
    return {X, y, features: feats};
}

const data = [
    {area:600, rooms:2, age:10, price:150},
    {area:900, rooms:3, age:5,  price:220},
    {area:1200,rooms:4, age:8,  price:300},
    {area:500, rooms:1, age:20, price:110},
];
const {X, y, features} = extractXY(data, 'price');
console.log('Features:', features);  // ['area','rooms','age']
console.log('X[0]:', X[0]);          // [600,2,10]
console.log('y:',    y);             // [150,220,300,110]
JS,
            ],
            [
                'title' => 'Generate Synthetic Linear Dataset', 'difficulty' => 'easy',
                'description' => 'Create a synthetic regression dataset with a linear relationship and Gaussian noise using the Box-Muller transform. Useful for testing ML algorithms.',
                'solution_code' => <<<'JS'
function gaussian(mean=0, std=1) {
    const u1=Math.random(), u2=Math.random();
    return mean + std * Math.sqrt(-2*Math.log(u1)) * Math.cos(2*Math.PI*u2);
}
function makeLinear(n=20, slope=2, intercept=5, noise=1) {
    return Array.from({length:n}, (_, i) => {
        const x = i / n * 10;
        return {x:+x.toFixed(2), y:+(slope*x+intercept+gaussian(0,noise)).toFixed(3)};
    });
}

const data = makeLinear(10, 2, 5, 0.5);
data.forEach(({x,y}) => console.log(`x=${x}  y=${y}`));
// y ≈ 2x + 5  with σ=0.5 noise
JS,
            ],
            [
                'title' => 'Pearson Correlation Matrix', 'difficulty' => 'easy',
                'description' => 'Compute pairwise Pearson correlation coefficients for all numeric columns. Equivalent to df.corr(). Correlation near ±1 indicates a strong linear relationship.',
                'solution_code' => <<<'JS'
function pearson(X, Y) {
    const n=X.length, mx=X.reduce((a,b)=>a+b,0)/n, my=Y.reduce((a,b)=>a+b,0)/n;
    const num=X.reduce((s,x,i)=>s+(x-mx)*(Y[i]-my),0);
    const den=Math.sqrt(X.reduce((s,x)=>s+(x-mx)**2,0)*Y.reduce((s,y)=>s+(y-my)**2,0));
    return den?+(num/den).toFixed(4):0;
}
function corrMatrix(data) {
    const cols=Object.keys(data[0]);
    return Object.fromEntries(cols.map(c1=>[c1,Object.fromEntries(cols.map(c2=>[c2,pearson(data.map(r=>r[c1]),data.map(r=>r[c2]))]))]))
}

const data=[{x:1,y:2,z:9},{x:2,y:4,z:7},{x:3,y:6,z:5},{x:4,y:8,z:3},{x:5,y:10,z:1}];
const m=corrMatrix(data);
console.log('x↔y:', m.x.y);   // 1.0  (perfect positive)
console.log('x↔z:', m.x.z);   // -1.0 (perfect negative)
JS,
            ],
            [
                'title' => 'Variance and Standard Deviation', 'difficulty' => 'easy',
                'description' => 'Compute population variance (divide by n) and sample variance (divide by n−1). Standard deviation is the square root of variance. Measures spread of a distribution.',
                'solution_code' => <<<'JS'
function describe(data) {
    const n = data.length;
    const mean = data.reduce((a, b) => a + b, 0) / n;
    const popVar  = data.reduce((s,x)=>s+(x-mean)**2,0) / n;
    const sampVar = data.reduce((s,x)=>s+(x-mean)**2,0) / (n-1);
    const min=Math.min(...data), max=Math.max(...data);
    return {n, mean:+mean.toFixed(4), popStd:+Math.sqrt(popVar).toFixed(4), sampStd:+Math.sqrt(sampVar).toFixed(4), min, max, range:max-min};
}

// Classic textbook example: mean=5, std=2
console.log(describe([2,4,4,4,5,5,7,9]));
console.log(describe([10,20,30,40,50]));
JS,
            ],
            [
                'title' => 'Overfitting Demonstration', 'difficulty' => 'easy',
                'description' => 'Show how increasing model complexity (polynomial degree) reduces training error but eventually leads to overfitting. High-degree polynomials memorize noise instead of learning the true pattern.',
                'solution_code' => <<<'JS'
function polyPredict(x, coeffs) {
    return coeffs.reduce((s, c, i) => s + c * x**i, 0);
}
function rmse(ys, preds) {
    return Math.sqrt(ys.reduce((s,y,i)=>s+(y-preds[i])**2,0)/ys.length);
}

// Degree-1 (underfit), degree-2 (good fit), degree-6 (overfit) on y=x²+noise
const xs=[0,1,2,3,4,5], ys=[0.1,1.2,3.9,9.1,16.2,24.8];

// Hand-fit models for demo
const models=[
    {degree:1, coeffs:[0,5],   label:'Underfit (degree 1)'},
    {degree:2, coeffs:[0,0,1], label:'Good fit (degree 2)'},
];
models.forEach(({label,coeffs})=>{
    const preds=xs.map(x=>polyPredict(x,coeffs));
    console.log(`${label}: RMSE=${rmse(ys,preds).toFixed(2)}`);
});
// Overfit: memorize all points perfectly → RMSE=0 but generalizes badly
console.log('Overfit (memorized): RMSE=0 on train, high on new data');
JS,
            ],
            [
                'title' => 'Underfitting Demonstration', 'difficulty' => 'easy',
                'description' => 'Compare a constant model (extreme underfitting) against a linear model. Shows how a model too simple to capture the true relationship has high bias error regardless of training data size.',
                'solution_code' => <<<'JS'
function rmse(actual, pred) {
    return Math.sqrt(actual.reduce((s,y,i)=>s+(y-pred[i])**2,0)/actual.length);
}

// True relation: y ≈ 3x + 2 (linear)
const xs=[0,1,2,3,4,5,6,7,8,9];
const ys=[2.1,5.0,8.2,11.1,14.0,17.1,20.0,22.9,26.1,29.0];

// Underfit: constant model (just the mean)
const mean=ys.reduce((a,b)=>a+b,0)/ys.length;
const constPreds=ys.map(()=>mean);
console.log('Constant model RMSE:', rmse(ys,constPreds).toFixed(3));  // high

// Better: linear model (y ≈ 3x + 2)
const linPreds=xs.map(x=>3*x+2);
console.log('Linear model RMSE:', rmse(ys,linPreds).toFixed(3));       // low

// What underfitting looks like:
console.log('\nConst predictions:', constPreds.map(v=>v.toFixed(1)));
console.log('Linear predictions:', linPreds.map(v=>v.toFixed(1)));
JS,
            ],
            [
                'title' => 'Generate Random Data with Gaussian Noise', 'difficulty' => 'easy',
                'description' => 'Synthesize datasets with controlled random noise using the Box-Muller transform. Used to test ML algorithms in reproducible, noise-controlled experiments.',
                'solution_code' => <<<'JS'
function gaussian(mu=0, sigma=1) {
    const u1=Math.random(), u2=Math.random();
    return mu + sigma * Math.sqrt(-2*Math.log(u1)) * Math.cos(2*Math.PI*u2);
}
function makeClassification(n=20, noise=0.5) {
    const data = [];
    for (let i = 0; i < n/2; i++) data.push({x:gaussian(1,noise), y:gaussian(1,noise), label:0});
    for (let i = 0; i < n/2; i++) data.push({x:gaussian(4,noise), y:gaussian(4,noise), label:1});
    return data;
}

const data = makeClassification(10, 0.3);
data.forEach(({x,y,label})=>console.log(`class ${label}: (${x.toFixed(2)}, ${y.toFixed(2)})`));
// class 0 points cluster around (1,1), class 1 around (4,4)
JS,
            ],
            [
                'title' => 'Fisher-Yates Shuffle', 'difficulty' => 'easy',
                'description' => 'Uniformly random permutation of an array in O(n) time. Used to shuffle training data before each epoch to prevent order bias in gradient descent.',
                'solution_code' => <<<'JS'
function shuffle(arr) {
    const a = [...arr];
    for (let i = a.length-1; i > 0; i--) {
        const j = Math.floor(Math.random()*(i+1));
        [a[i],a[j]] = [a[j],a[i]];
    }
    return a;
}

const data = Array.from({length:10}, (_,i)=>i+1);
console.log('Original:', data);
console.log('Shuffled:', shuffle(data));
console.log('Shuffled:', shuffle(data));  // different each time

// Verify uniform distribution (sort shuffled should equal original)
const sh=shuffle(data);
console.log('All values preserved:', JSON.stringify([...sh].sort((a,b)=>a-b))===JSON.stringify(data));
JS,
            ],
            [
                'title' => 'Split Dataset into Mini-Batches', 'difficulty' => 'easy',
                'description' => 'Divide a dataset into fixed-size chunks for mini-batch gradient descent. The final batch may be smaller if the dataset size is not divisible by batch size.',
                'solution_code' => <<<'JS'
function* batchGen(data, batchSize) {
    for (let i = 0; i < data.length; i += batchSize) yield data.slice(i, i+batchSize);
}

function makeBatches(data, batchSize) {
    return [...batchGen(data, batchSize)];
}

const dataset = Array.from({length:100}, (_,i)=>({id:i, x:i*0.1}));
const batches = makeBatches(dataset, 32);

console.log(`${dataset.length} samples → ${batches.length} batches`);
batches.forEach((b,i)=>console.log(`Batch ${i+1}: ${b.length} samples (ids ${b[0].id}–${b.at(-1).id})`));
// Batch 1: 32 samples (ids 0–31)
// Batch 2: 32 samples (ids 32–63)
// Batch 3: 32 samples (ids 64–95)
// Batch 4:  4 samples (ids 96–99)
JS,
            ],
            [
                'title' => 'Bootstrap Confidence Interval', 'difficulty' => 'easy',
                'description' => 'Estimate a 95% confidence interval for the mean using bootstrap resampling. Resample with replacement n_boot times, compute the statistic each time, and report the 2.5–97.5 percentile range.',
                'solution_code' => <<<'JS'
function bootstrap(data, statFn=arr=>arr.reduce((a,b)=>a+b,0)/arr.length, nBoot=1000, alpha=0.05) {
    const stats=Array.from({length:nBoot},()=>{
        const sample=Array.from({length:data.length},()=>data[Math.floor(Math.random()*data.length)]);
        return statFn(sample);
    });
    stats.sort((a,b)=>a-b);
    const lo=stats[Math.floor(nBoot*alpha/2)];
    const hi=stats[Math.floor(nBoot*(1-alpha/2))];
    return{lo:+lo.toFixed(4),hi:+hi.toFixed(4),estimate:+statFn(data).toFixed(4)};
}

const data=[4.2,5.1,3.8,6.0,4.7,5.5,3.9,4.8,5.3,4.1,6.2,5.0,4.4,4.9,5.7];
const ci=bootstrap(data);
console.log(`Mean estimate: ${ci.estimate}`);
console.log(`95% CI: [${ci.lo}, ${ci.hi}]`);

// Also works for median
const ciMedian=bootstrap(data,arr=>{const s=[...arr].sort((a,b)=>a-b);return s[Math.floor(s.length/2)];});
console.log(`Median 95% CI: [${ciMedian.lo}, ${ciMedian.hi}]`);
JS,
            ],
        ];
    }

    // ─── INTERMEDIATE / MEDIUM (461–500) ─────────────────────────────────────

    private function intermediateProblems(): array
    {
        return [
            [
                'title' => 'Multivariate Linear Regression', 'difficulty' => 'medium',
                'description' => 'Extend linear regression to multiple input features using batch gradient descent. Learns a hyperplane w·x+b that minimizes MSE across all training examples.',
                'solution_code' => <<<'JS'
function linReg(X, y, lr=0.01, epochs=1000) {
    let w=new Array(X[0].length).fill(0), b=0;
    const pred=x=>x.reduce((s,xi,i)=>s+xi*w[i],b);
    for(let e=0;e<epochs;e++){
        const dw=new Array(w.length).fill(0); let db=0;
        X.forEach((x,j)=>{const err=pred(x)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi-lr*dw[i]/X.length); b-=lr*db/X.length;
    }
    return{w:w.map(v=>+v.toFixed(4)),b:+b.toFixed(4),predict:pred};
}

// Features: [area, rooms]
const X=[[600,2],[800,3],[1000,3],[1200,4],[1400,4]];
const y=[150,190,230,280,320];
const m=linReg(X,y,0.00001,5000);
console.log('Weights:',m.w, 'Bias:',m.b);
console.log('Predict [1100,3]:',m.predict([1100,3]).toFixed(1));
JS,
            ],
            [
                'title' => 'House Price Predictor', 'difficulty' => 'medium',
                'description' => 'Train a linear regression model on house features (area, rooms, age) and use it to predict prices. Demonstrates a complete ML pipeline: train, evaluate, predict.',
                'solution_code' => <<<'JS'
function trainModel(X, y, lr=0.0001, epochs=3000) {
    let w=new Array(X[0].length).fill(0), b=0;
    const pred=x=>x.reduce((s,xi,i)=>s+xi*w[i],b);
    for(let e=0;e<epochs;e++){
        const dw=new Array(w.length).fill(0); let db=0;
        X.forEach((x,j)=>{const err=pred(x)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi-lr*dw[i]/X.length);b-=lr*db/X.length;
    }
    const rmse=Math.sqrt(X.reduce((s,x,i)=>s+(pred(x)-y[i])**2,0)/X.length);
    return{predict:pred,rmse:+rmse.toFixed(2)};
}

// [area, rooms, age_years]
const X=[[800,2,15],[1200,3,10],[1500,4,5],[600,1,25],[2000,5,2]];
const y=[180,260,320,130,420]; // prices in $k
const model=trainModel(X,y);
console.log('RMSE:',model.rmse);
[[1000,3,8],[1800,4,3]].forEach(x=>console.log(`${x}→$${model.predict(x).toFixed(0)}k`));
JS,
            ],
            [
                'title' => 'Logistic Regression Classifier', 'difficulty' => 'medium',
                'description' => 'Train logistic regression with L2 regularization (weight decay) to classify binary outcomes. L2 penalty λ‖w‖² prevents overfitting on small datasets.',
                'solution_code' => <<<'JS'
function logRegL2(X, y, lr=0.1, epochs=300, lambda=0.01) {
    const sig=x=>1/(1+Math.exp(-x));
    let w=new Array(X[0].length).fill(0), b=0;
    const pred=x=>sig(x.reduce((s,xi,i)=>s+xi*w[i],b));
    for(let e=0;e<epochs;e++){
        const dw=new Array(w.length).fill(0); let db=0;
        X.forEach((x,j)=>{const err=pred(x)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi*(1-lr*lambda/X.length)-lr*dw[i]/X.length);
        b-=lr*db/X.length;
    }
    const classify=x=>pred(x)>=0.5?1:0;
    const acc=X.filter((x,i)=>classify(x)===y[i]).length/X.length;
    return{classify,acc:+acc.toFixed(4)};
}

const X=[[1,2],[2,3],[3,1],[6,5],[5,7],[7,6]];
const y=[0,0,0,1,1,1];
const{classify,acc}=logRegL2(X,y);
console.log('Accuracy:',acc);
console.log('[2,2]:',classify([2,2]),'[6,6]:',classify([6,6]));  // 0, 1
JS,
            ],
            [
                'title' => 'Naive Bayes Spam Detector', 'difficulty' => 'medium',
                'description' => 'Build a multinomial Naive Bayes text classifier that uses Laplace smoothing to classify messages as spam or ham based on word frequencies.',
                'solution_code' => <<<'JS'
function spamDetector(spamMsgs, hamMsgs) {
    const tok=t=>t.toLowerCase().match(/\w+/g)||[];
    const freq=(msgs)=>{const f={},all=msgs.flatMap(tok);all.forEach(w=>f[w]=(f[w]||0)+1);return{f,n:all.length};};
    const spam=freq(spamMsgs), ham=freq(hamMsgs);
    const V=new Set([...Object.keys(spam.f),...Object.keys(ham.f)]).size;
    return msg=>{
        const words=tok(msg);
        let ls=Math.log(0.5),lh=Math.log(0.5);
        words.forEach(w=>{
            ls+=Math.log(((spam.f[w]||0)+1)/(spam.n+V));
            lh+=Math.log(((ham.f[w]||0)+1)/(ham.n+V));
        });
        return{label:ls>lh?'spam':'ham',spamScore:+ls.toFixed(2),hamScore:+lh.toFixed(2)};
    };
}

const clf=spamDetector(
    ['win free money now','click here claim prize','free offer limited time win'],
    ['meeting at 3pm tomorrow','project report ready','lunch plans today']
);
console.log(clf('free money click here'));    // spam
console.log(clf('meeting notes for review')); // ham
JS,
            ],
            [
                'title' => 'Decision Tree Classifier', 'difficulty' => 'medium',
                'description' => 'Build a binary decision tree using Gini impurity to find the best split at each node. Recursively partition data until maxDepth or pure leaves are reached.',
                'solution_code' => <<<'JS'
function gini(ys){const n=ys.length,f={};ys.forEach(y=>f[y]=(f[y]||0)+1);return 1-Object.values(f).reduce((s,c)=>s+(c/n)**2,0);}
function majority(ys){const f={};ys.forEach(y=>f[y]=(f[y]||0)+1);return Object.entries(f).sort((a,b)=>b[1]-a[1])[0][0];}
function split(X,y){
    let best={gain:-1};
    X[0].forEach((_,f)=>{
        [...new Set(X.map(r=>r[f]))].sort((a,b)=>a-b).slice(0,-1).forEach((v,i,a)=>{
            const t=(v+a[i+1??v])/2;
            const L=y.filter((_,k)=>X[k][f]<=t),R=y.filter((_,k)=>X[k][f]>t);
            const g=gini(y)-(L.length/y.length)*gini(L)-(R.length/y.length)*gini(R);
            if(g>best.gain)best={gain:g,f,t};
        });
    });
    return best;
}
function build(X,y,depth=0,max=3){
    if(depth>=max||new Set(y).size===1)return{leaf:majority(y)};
    const{f,t}=split(X,y);
    const li=X.map((_,i)=>i).filter(i=>X[i][f]<=t),ri=X.map((_,i)=>i).filter(i=>X[i][f]>t);
    return{f,t,L:build(li.map(i=>X[i]),li.map(i=>y[i]),depth+1,max),R:build(ri.map(i=>X[i]),ri.map(i=>y[i]),depth+1,max)};
}
const prd=(n,x)=>n.leaf!==undefined?n.leaf:x[n.f]<=n.t?prd(n.L,x):prd(n.R,x);
const X=[[1,1],[2,2],[3,3],[7,7],[8,8],[9,9]],y=[0,0,0,1,1,1];
const tree=build(X,y);
console.log(prd(tree,[2,2]),prd(tree,[8,8]));  // 0 1
JS,
            ],
            [
                'title' => 'Random Forest Classifier', 'difficulty' => 'medium',
                'description' => 'Train an ensemble of decision stumps on bootstrap samples and aggregate predictions by majority vote. Reduces variance compared to a single tree.',
                'solution_code' => <<<'JS'
function gini(ys){const n=ys.length,f={};ys.forEach(y=>f[y]=(f[y]||0)+1);return 1-Object.values(f).reduce((s,c)=>s+(c/n)**2,0);}
function majority(ys){const f={};ys.forEach(y=>f[y]=(f[y]||0)+1);return Object.entries(f).sort((a,b)=>b[1]-a[1])[0][0];}
function stump(X,y){
    let best={gain:-1,f:0,t:0};
    X[0].forEach((_,f)=>[...new Set(X.map(r=>r[f]))].sort((a,b)=>a-b).forEach(t=>{
        const L=y.filter((_,k)=>X[k][f]<=t),R=y.filter((_,k)=>X[k][f]>t);
        const g=gini(y)-(L.length/y.length)*gini(L)-(R.length/y.length)*gini(R);
        if(g>best.gain)best={gain:g,f,t,Lp:majority(L||[majority(y)]),Rp:majority(R||[majority(y)])};
    }));
    return x=>x[best.f]<=best.t?best.Lp:best.Rp;
}
function bootstrap(X,y){const idx=X.map(()=>Math.floor(Math.random()*X.length));return{X:idx.map(i=>X[i]),y:idx.map(i=>y[i])};}
function rf(X,y,n=15){
    const trees=Array.from({length:n},()=>{const{X:bX,y:bY}=bootstrap(X,y);return stump(bX,bY);});
    return x=>{const v={};trees.forEach(t=>{const p=t(x);v[p]=(v[p]||0)+1;});return Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];};
}
const X=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]],y=[0,0,0,1,1,1];
const predict=rf(X,y);
console.log(predict([1.5,1.5]),predict([5.5,5.5]));  // 0 1
JS,
            ],
            [
                'title' => 'Classification Accuracy and Error Rate', 'difficulty' => 'medium',
                'description' => 'Compute accuracy (fraction correctly classified), error rate (1−accuracy), and per-class accuracy. Essential first evaluation metrics for any classifier.',
                'solution_code' => <<<'JS'
function classifyMetrics(actual, predicted) {
    const correct = actual.filter((y,i)=>y===predicted[i]).length;
    const accuracy = correct/actual.length;
    const classes = [...new Set(actual)];
    const perClass = Object.fromEntries(classes.map(c=>{
        const idx=actual.reduce((a,y,i)=>y===c?[...a,i]:a,[]);
        const acc=idx.filter(i=>predicted[i]===c).length/idx.length;
        return[c,+acc.toFixed(4)];
    }));
    return{accuracy:+accuracy.toFixed(4),errorRate:+(1-accuracy).toFixed(4),perClass,n:actual.length};
}

const actual   =[0,0,1,1,2,2,0,1,2,0];
const predicted=[0,1,1,0,2,2,0,1,1,0];
const result=classifyMetrics(actual,predicted);
console.log('Accuracy:',result.accuracy);    // 0.7
console.log('Error:',result.errorRate);      // 0.3
console.log('Per-class:',result.perClass);
JS,
            ],
            [
                'title' => 'Multi-Class Confusion Matrix', 'difficulty' => 'medium',
                'description' => 'Build a confusion matrix for n-class classification. Entry [i][j] is the count of actual class i predicted as class j. The diagonal shows correct predictions.',
                'solution_code' => <<<'JS'
function confusionMatrix(actual, predicted) {
    const classes=[...new Set([...actual,...predicted])].sort();
    const idx=Object.fromEntries(classes.map((c,i)=>[c,i]));
    const mat=Array.from({length:classes.length},()=>new Array(classes.length).fill(0));
    actual.forEach((y,i)=>mat[idx[y]][idx[predicted[i]]]++);
    return{matrix:mat,classes};
}

const actual   =['cat','dog','cat','bird','dog','cat','bird'];
const predicted=['cat','dog','bird','bird','cat','cat','bird'];
const{matrix,classes}=confusionMatrix(actual,predicted);
console.log('Classes:',classes);
console.log('Confusion matrix:');
matrix.forEach((row,i)=>console.log(classes[i].padEnd(6),'|',row));
// diagonal = correct, off-diagonal = errors
JS,
            ],
            [
                'title' => 'Precision, Recall, and F1 Score per Class', 'difficulty' => 'medium',
                'description' => 'Compute macro-averaged precision, recall, and F1 for multi-class classification. Macro-average treats each class equally regardless of class frequency.',
                'solution_code' => <<<'JS'
function multiClassMetrics(actual, predicted) {
    const classes=[...new Set(actual)];
    const perClass=classes.map(c=>{
        const tp=actual.filter((y,i)=>y===c&&predicted[i]===c).length;
        const fp=actual.filter((y,i)=>y!==c&&predicted[i]===c).length;
        const fn=actual.filter((y,i)=>y===c&&predicted[i]!==c).length;
        const pr=tp/(tp+fp)||0, re=tp/(tp+fn)||0;
        return{class:c,precision:+pr.toFixed(4),recall:+re.toFixed(4),f1:+(2*pr*re/(pr+re)||0).toFixed(4)};
    });
    const macro=perClass.reduce((s,m)=>({precision:s.precision+m.precision/classes.length,recall:s.recall+m.recall/classes.length,f1:s.f1+m.f1/classes.length}),{precision:0,recall:0,f1:0});
    return{perClass,macro:{precision:+macro.precision.toFixed(4),recall:+macro.recall.toFixed(4),f1:+macro.f1.toFixed(4)}};
}

const a=['cat','dog','bird','cat','dog'], p=['cat','cat','bird','dog','dog'];
const r=multiClassMetrics(a,p);
console.log('Per-class:',r.perClass);
console.log('Macro:',r.macro);
JS,
            ],
            [
                'title' => 'ROC Curve and AUC Score', 'difficulty' => 'medium',
                'description' => 'Generate the Receiver Operating Characteristic curve by sweeping a probability threshold, computing TPR and FPR at each point. AUC measures the area under this curve.',
                'solution_code' => <<<'JS'
function roc(actual, scores) {
    const thresholds=[...new Set(scores)].sort((a,b)=>b-a);
    const pos=actual.filter(y=>y===1).length, neg=actual.length-pos;
    const points=thresholds.map(t=>{
        const tp=actual.filter((y,i)=>y===1&&scores[i]>=t).length;
        const fp=actual.filter((y,i)=>y===0&&scores[i]>=t).length;
        return{tpr:tp/pos,fpr:fp/neg,threshold:t};
    });
    // AUC via trapezoidal rule
    let auc=0;
    for(let i=1;i<points.length;i++) auc+=Math.abs(points[i].fpr-points[i-1].fpr)*(points[i].tpr+points[i-1].tpr)/2;
    return{points,auc:+auc.toFixed(4)};
}

const actual=[1,1,0,1,0,1,0,0,1,0];
const scores=[.9,.8,.7,.6,.55,.5,.4,.3,.2,.1];
const{points,auc}=roc(actual,scores);
console.log('AUC:',auc);  // ~1.0 (perfect ordering)
points.slice(0,4).forEach(p=>console.log(`threshold=${p.threshold}: TPR=${p.tpr.toFixed(2)}, FPR=${p.fpr.toFixed(2)}`));
JS,
            ],
            [
                'title' => 'Correlation-Based Feature Selection', 'difficulty' => 'medium',
                'description' => 'Select features with the highest absolute Pearson correlation with the target variable. Equivalent to SelectKBest with the f_regression scoring function in sklearn.',
                'solution_code' => <<<'JS'
function pearson(A,B){const n=A.length,mA=A.reduce((a,b)=>a+b,0)/n,mB=B.reduce((a,b)=>a+b,0)/n;const num=A.reduce((s,a,i)=>s+(a-mA)*(B[i]-mB),0);const den=Math.sqrt(A.reduce((s,a)=>s+(a-mA)**2,0)*B.reduce((s,b)=>s+(b-mB)**2,0));return den?num/den:0;}

function selectKBest(X, y, k=2) {
    const scores=X[0].map((_,j)=>({j,corr:Math.abs(pearson(X.map(r=>r[j]),y))}));
    scores.sort((a,b)=>b.corr-a.corr);
    const top=scores.slice(0,k).map(s=>s.j);
    return{selectedIdx:top,scores:scores.map(s=>({feature:`x${s.j}`,corr:+s.corr.toFixed(4)})),Xsel:X.map(r=>top.map(j=>r[j]))};
}

// Features: [relevant, noisy, relevant2, noise2]
const X=[[1,9,2,3],[2,1,4,7],[3,5,6,2],[4,3,8,8],[5,7,10,4]];
const y=[2,4,6,8,10];
const{selectedIdx,scores}=selectKBest(X,y,2);
console.log('Feature scores:',scores);
console.log('Selected features:',selectedIdx);  // [0,2] (linearly correlated)
JS,
            ],
            [
                'title' => 'Feature Scaling Comparison', 'difficulty' => 'medium',
                'description' => 'Compare Min-Max, Z-Score, and Robust scaling (subtract median, divide by IQR). Show how each handles outliers differently. Choose based on the algorithm and data distribution.',
                'solution_code' => <<<'JS'
function scalingComparison(data) {
    const sorted=[...data].sort((a,b)=>a-b),n=data.length;
    const min=Math.min(...data),max=Math.max(...data);
    const mean=data.reduce((a,b)=>a+b,0)/n;
    const std=Math.sqrt(data.reduce((s,x)=>s+(x-mean)**2,0)/n);
    const median=sorted[Math.floor(n/2)];
    const q1=sorted[Math.floor(n*0.25)],q3=sorted[Math.floor(n*0.75)],iqr=q3-q1||1;
    return{
        minmax:   data.map(x=>+((x-min)/(max-min||1)).toFixed(3)),
        zscore:   data.map(x=>+((x-mean)/(std||1)).toFixed(3)),
        robust:   data.map(x=>+((x-median)/iqr).toFixed(3)),
    };
}

// Data with an outlier
const data=[1,2,3,4,5,100];
const r=scalingComparison(data);
console.log('Min-Max:', r.minmax); // outlier → 1.0
console.log('Z-Score:', r.zscore); // outlier → high z
console.log('Robust:',  r.robust); // outlier less extreme
JS,
            ],
            [
                'title' => 'Handle Class Imbalance (Random Oversampling)', 'difficulty' => 'medium',
                'description' => 'Balance an imbalanced dataset by randomly duplicating minority class samples until all classes have equal representation. Simple baseline before trying SMOTE.',
                'solution_code' => <<<'JS'
function randomOversample(X, y) {
    const classes=[...new Set(y)];
    const groups=Object.fromEntries(classes.map(c=>[c,X.filter((_,i)=>y[i]===c)]));
    const maxSize=Math.max(...Object.values(groups).map(g=>g.length));
    const newX=[],newY=[];
    for(const [cls,samples] of Object.entries(groups)){
        newX.push(...samples); newY.push(...samples.map(()=>cls));
        while(newX.filter((_,i)=>newY[i]===cls).length<maxSize){
            const s=samples[Math.floor(Math.random()*samples.length)];
            newX.push(s); newY.push(cls);
        }
    }
    return{X:newX,y:newY};
}

const X=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6],[5.5,5.5],[6,6],[5,6.5],[4.5,5.5]];
const y=['A','A','A','B','B','B','B','B','B','B'];  // 3 A, 7 B (imbalanced)
const{X:bX,y:bY}=randomOversample(X,y);
const counts={}; bY.forEach(c=>counts[c]=(counts[c]||0)+1);
console.log('After oversampling:',counts);  // {A:7,B:7}
JS,
            ],
            [
                'title' => 'SMOTE (Synthetic Minority Oversampling)', 'difficulty' => 'medium',
                'description' => 'Generate synthetic minority samples by interpolating between existing minority points and their k nearest neighbors. Produces more varied data than simple duplication.',
                'solution_code' => <<<'JS'
function dist(a,b){return Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));}
function knnNeighbors(points,query,k){return points.map((p,i)=>({i,d:dist(p,query)})).sort((a,b)=>a.d-b.d).slice(1,k+1).map(x=>x.i);}

function smote(minority, n=5, k=3) {
    const synthetic=[];
    while(synthetic.length<n){
        const seed=minority[Math.floor(Math.random()*minority.length)];
        const nbrIdx=knnNeighbors(minority,seed,k);
        const nbr=minority[nbrIdx[Math.floor(Math.random()*nbrIdx.length)]];
        const lambda=Math.random();
        synthetic.push(seed.map((s,i)=>+(s+lambda*(nbr[i]-s)).toFixed(3)));
    }
    return synthetic;
}

const minority=[[1,1],[1.5,2],[2,1.5],[1.2,1.8],[1.8,1.2]];
const synth=smote(minority,5,3);
console.log('Original minority:',minority);
console.log('Synthetic samples:',synth);
// Synthetic points lie between existing minority points
JS,
            ],
            [
                'title' => 'K-Fold Cross Validation', 'difficulty' => 'medium',
                'description' => 'Split data into k folds, train on k-1 and evaluate on 1, cycling through all folds. Returns mean and std of accuracy across folds. More reliable than a single train/test split.',
                'solution_code' => <<<'JS'
function kFoldCV(X, y, k=5, trainEval) {
    const n=X.length, foldSize=Math.floor(n/k);
    const scores=[];
    for(let i=0;i<k;i++){
        const testIdx=Array.from({length:foldSize},(_,j)=>i*foldSize+j);
        const testSet=new Set(testIdx);
        const Xtr=X.filter((_,j)=>!testSet.has(j)),ytr=y.filter((_,j)=>!testSet.has(j));
        const Xte=X.filter((_,j)=>testSet.has(j)),yte=y.filter((_,j)=>testSet.has(j));
        scores.push(trainEval(Xtr,ytr,Xte,yte));
    }
    const mean=scores.reduce((a,b)=>a+b,0)/k;
    const std=Math.sqrt(scores.reduce((s,x)=>s+(x-mean)**2,0)/k);
    return{scores:scores.map(s=>+s.toFixed(4)),mean:+mean.toFixed(4),std:+std.toFixed(4)};
}

// Simple accuracy evaluator using KNN(k=3)
const dist=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
const knn=(Xtr,ytr,x,k=3)=>{const v={};Xtr.map((p,i)=>({d:dist(p,x),l:ytr[i]})).sort((a,b)=>a.d-b.d).slice(0,k).forEach(({l})=>v[l]=(v[l]||0)+1);return Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];};
const X=[[1,1],[1,2],[2,1],[2,2],[5,5],[5,6],[6,5],[6,6],[3.5,3.5],[4,4]];
const y=[0,0,0,0,1,1,1,1,1,0];
console.log(kFoldCV(X,y,5,(Xtr,ytr,Xte,yte)=>Xte.filter((x,i)=>knn(Xtr,ytr,x)===yte[i]).length/Xte.length));
JS,
            ],
            [
                'title' => 'Grid Search Hyperparameter Tuning', 'difficulty' => 'medium',
                'description' => 'Exhaustively search a parameter grid using cross-validation to find the best hyperparameter combination. Equivalent to sklearn GridSearchCV.',
                'solution_code' => <<<'JS'
function gridSearch(paramGrid, trainEval, X, y, k=3) {
    // Generate all param combinations
    const keys=Object.keys(paramGrid);
    const combos=[{}];
    keys.forEach(key=>paramGrid[key].forEach(val=>{
        const newCombos=combos.map(c=>({...c,[key]:val}));
        combos.splice(0,combos.length,...combos.flatMap(c=>paramGrid[key].map(v=>({...c,[key]:v}))));
    }));
    // Dedupe
    const seen=new Set(), unique=combos.filter(c=>{const k=JSON.stringify(c);return seen.has(k)?false:(seen.add(k),true);});
    const results=unique.map(params=>{
        const score=trainEval(X,y,params,k);
        return{params,score:+score.toFixed(4)};
    });
    results.sort((a,b)=>b.score-a.score);
    return{best:results[0],results};
}

// KNN with variable k
const knnEval=(X,y,{k},folds)=>{const n=X.length,fs=Math.floor(n/folds);let s=0;for(let i=0;i<folds;i++){const ti=new Set(Array.from({length:fs},(_,j)=>i*fs+j));const Xtr=X.filter((_,j)=>!ti.has(j)),ytr=y.filter((_,j)=>!ti.has(j)),Xte=X.filter((_,j)=>ti.has(j)),yte=y.filter((_,j)=>ti.has(j));const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));s+=Xte.filter((x,i)=>{const v={};Xtr.map((p,j)=>({d:d(p,x),l:ytr[j]})).sort((a,b)=>a.d-b.d).slice(0,k).forEach(({l})=>v[l]=(v[l]||0)+1);return Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0]===yte[i];}).length/Xte.length;}return s/folds;};
const X=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]],y=[0,0,0,1,1,1];
console.log(gridSearch({k:[1,3,5]},knnEval,X,y).best);
JS,
            ],
            [
                'title' => 'Random Search Hyperparameter Tuning', 'difficulty' => 'medium',
                'description' => 'Randomly sample parameter combinations from a search space for a fixed number of trials. More efficient than grid search for high-dimensional parameter spaces.',
                'solution_code' => <<<'JS'
function randomSearch(paramSpace, trainEval, X, y, nTrials=20, k=3) {
    const sample=space=>Object.fromEntries(Object.entries(space).map(([key,{low,high,type}])=>{
        const v=low+Math.random()*(high-low);
        return[key,type==='int'?Math.round(v):+v.toFixed(4)];
    }));
    const results=Array.from({length:nTrials},()=>{
        const params=sample(paramSpace);
        return{params,score:+trainEval(X,y,params,k).toFixed(4)};
    });
    results.sort((a,b)=>b.score-a.score);
    return{best:results[0],top3:results.slice(0,3)};
}

// Example: tune lr and regularization for logistic regression
const lrEval=(X,y,{lr,lambda},folds)=>{const sig=x=>1/(1+Math.exp(-x));const n=X.length,fs=Math.floor(n/folds);let s=0;for(let i=0;i<folds;i++){let w=new Array(X[0].length).fill(0),b=0;const ti=new Set(Array.from({length:fs},(_,j)=>i*fs+j));const Xtr=X.filter((_,j)=>!ti.has(j)),ytr=y.filter((_,j)=>!ti.has(j)),Xte=X.filter((_,j)=>ti.has(j)),yte=y.filter((_,j)=>ti.has(j));for(let e=0;e<200;e++){const dw=new Array(w.length).fill(0);let db=0;Xtr.forEach((x,j)=>{const err=sig(x.reduce((s,xi,i)=>s+xi*w[i],b))-ytr[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});w=w.map((wi,i)=>wi*(1-lr*lambda/Xtr.length)-lr*dw[i]/Xtr.length);b-=lr*db/Xtr.length;}s+=Xte.filter((x,i)=>(sig(x.reduce((s,xi,j)=>s+xi*w[j],b))>=0.5?1:0)===yte[i]).length/Xte.length;}return s/folds;};
const X=[[1,2],[2,3],[3,1],[6,5],[5,7],[7,6]],y=[0,0,0,1,1,1];
console.log(randomSearch({lr:{low:.01,high:.5,type:'float'},lambda:{low:.001,high:.1,type:'float'}},lrEval,X,y,10).best);
JS,
            ],
            [
                'title' => 'Early Stopping', 'difficulty' => 'medium',
                'description' => 'Stop training when validation loss stops improving for a set number of consecutive epochs (patience). Prevents overfitting without manually tuning the number of epochs.',
                'solution_code' => <<<'JS'
function trainWithEarlyStopping(X, y, Xval, yval, lr=0.1, maxEpochs=1000, patience=20) {
    const sig=x=>1/(1+Math.exp(-x));
    let w=new Array(X[0].length).fill(0), b=0;
    const pred=x=>sig(x.reduce((s,xi,i)=>s+xi*w[i],b));
    const loss=(Xd,yd)=>-yd.reduce((s,y,i)=>{const p=pred(Xd[i]);return s+y*Math.log(p+1e-15)+(1-y)*Math.log(1-p+1e-15);},0)/yd.length;
    let bestLoss=Infinity, bestW=[...w], bestB=b, wait=0, stoppedAt=maxEpochs;
    for(let e=0;e<maxEpochs;e++){
        const dw=new Array(w.length).fill(0); let db=0;
        X.forEach((x,j)=>{const err=pred(x)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi-lr*dw[i]/X.length); b-=lr*db/X.length;
        const valLoss=loss(Xval,yval);
        if(valLoss<bestLoss-1e-4){bestLoss=valLoss;bestW=[...w];bestB=b;wait=0;}
        else if(++wait>=patience){stoppedAt=e+1;break;}
    }
    return{stoppedAt,bestValLoss:+bestLoss.toFixed(4),w:bestW,b:bestB};
}

const X=[[1,2],[2,3],[3,1],[6,5],[5,7],[7,6]],y=[0,0,0,1,1,1];
const r=trainWithEarlyStopping(X,y,[[2,2],[6,6]],[0,1]);
console.log('Stopped at epoch:',r.stoppedAt);
console.log('Best val loss:',r.bestValLoss);
JS,
            ],
            [
                'title' => 'Permutation Feature Importance', 'difficulty' => 'medium',
                'description' => 'Measure feature importance by shuffling each feature column and measuring the drop in model accuracy. A large accuracy drop means the feature is important.',
                'solution_code' => <<<'JS'
function permutationImportance(X, y, modelPredict, baselineAcc, nRepeats=5) {
    const nFeats=X[0].length;
    return Array.from({length:nFeats},(_,f)=>{
        const drops=Array.from({length:nRepeats},()=>{
            const Xp=X.map(r=>[...r]);
            const col=X.map(r=>r[f]);
            const shuffled=[...col].sort(()=>Math.random()-.5);
            Xp.forEach((r,i)=>r[f]=shuffled[i]);
            const acc=Xp.filter((x,i)=>modelPredict(x)===y[i]).length/X.length;
            return baselineAcc-acc;
        });
        return{feature:`x${f}`,importance:+(drops.reduce((a,b)=>a+b,0)/nRepeats).toFixed(4)};
    }).sort((a,b)=>b.importance-a.importance);
}

const X=[[1,9],[2,1],[3,8],[4,2],[5,7],[6,3]],y=[0,0,0,1,1,1];
const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
const predict=x=>{const v={};X.map((p,i)=>({d:d(p,x),l:y[i]})).sort((a,b)=>a.d-b.d).slice(0,3).forEach(({l})=>v[l]=(v[l]||0)+1);return Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];};
const base=X.filter((x,i)=>predict(x)===y[i]).length/X.length;
console.log(permutationImportance(X,y,predict,base));
// x0 should have higher importance (correlated with y), x1 is noise
JS,
            ],
            [
                'title' => 'Multi-Model Comparison', 'difficulty' => 'medium',
                'description' => 'Train multiple classifiers on the same dataset and compare their accuracy, training time, and prediction scores. Helps select the best model for a given task.',
                'solution_code' => <<<'JS'
function compareModels(X, y, models) {
    const{train,test}=(data=>{
        const a=[...data.map((_,i)=>i)].sort(()=>Math.random()-.5);
        const s=Math.floor(a.length*.8);
        return{train:a.slice(0,s),test:a.slice(s)};
    })(X);
    return models.map(({name,train:trainFn,predict:pFn})=>{
        const t0=Date.now();
        const model=trainFn(train.map(i=>X[i]),train.map(i=>y[i]));
        const trainTime=Date.now()-t0;
        const acc=test.filter(i=>pFn(model,X[i])===y[i]).length/test.length;
        return{name,accuracy:+acc.toFixed(4),trainTimeMs:trainTime};
    }).sort((a,b)=>b.accuracy-a.accuracy);
}

const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
const X=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6],[3,3],[4,3],[3,4]];
const y=[0,0,0,1,1,1,0,1,0];
const results=compareModels(X,y,[
    {name:'KNN-1',train:(Xtr,ytr)=>({Xtr,ytr}),predict:(m,x)=>{const v={};m.Xtr.map((p,i)=>({d:d(p,x),l:m.ytr[i]})).sort((a,b)=>a.d-b.d).slice(0,1).forEach(({l})=>v[l]=(v[l]||0)+1);return+Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];}},
    {name:'KNN-3',train:(Xtr,ytr)=>({Xtr,ytr}),predict:(m,x)=>{const v={};m.Xtr.map((p,i)=>({d:d(p,x),l:m.ytr[i]})).sort((a,b)=>a.d-b.d).slice(0,3).forEach(({l})=>v[l]=(v[l]||0)+1);return+Object.entries(v).sort((a,b)=>b[1]-a[1])[0][0];}},
]);
results.forEach(r=>console.log(`${r.name}: acc=${r.accuracy}, time=${r.trainTimeMs}ms`));
JS,
            ],
            [
                'title' => 'DBSCAN Clustering', 'difficulty' => 'medium',
                'description' => 'Density-Based Spatial Clustering of Applications with Noise. Groups closely packed points and marks outliers as noise (-1). Unlike K-Means, discovers clusters of arbitrary shape.',
                'solution_code' => <<<'JS'
function dbscan(data, eps=1.5, minPts=2) {
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    const labels=new Array(data.length).fill(-1);
    let cluster=0;
    function expand(idx, neighbors) {
        labels[idx]=cluster;
        let i=0;
        while(i<neighbors.length){
            const n=neighbors[i++];
            if(labels[n]===-1){
                labels[n]=cluster;
                const nn=data.map((_,k)=>d(data[n],data[k])<=eps?k:-1).filter(k=>k>=0);
                if(nn.length>=minPts) neighbors.push(...nn.filter(k=>!neighbors.includes(k)));
            } else if(labels[n]===-2) labels[n]=cluster;
        }
    }
    data.forEach((p,i)=>{
        if(labels[i]!==-1)return;
        const nb=data.map((_,k)=>d(p,data[k])<=eps?k:-1).filter(k=>k>=0);
        if(nb.length<minPts){labels[i]=-2;return;}  // noise=-2
        expand(i,nb); cluster++;
    });
    return labels.map(l=>l===-2?-1:l);
}

const data=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6],[10,10]];  // 2 clusters + 1 outlier
const labels=dbscan(data,1.5,2);
console.log('Labels:',labels);
// [0,0,0,1,1,1,-1]  → cluster 0, cluster 1, noise
JS,
            ],
            [
                'title' => 'Agglomerative (Single-Linkage) Clustering', 'difficulty' => 'medium',
                'description' => 'Bottom-up hierarchical clustering: start with each point as its own cluster, then iteratively merge the two clusters with the minimum distance between any pair of their members.',
                'solution_code' => <<<'JS'
function agglomerative(data, k=2) {
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    let clusters=data.map((_,i)=>[i]);
    const history=[];
    while(clusters.length>k){
        let best={dist:Infinity,i:0,j:1};
        for(let i=0;i<clusters.length;i++)
            for(let j=i+1;j<clusters.length;j++){
                const dist=Math.min(...clusters[i].flatMap(a=>clusters[j].map(b=>d(data[a],data[b]))));
                if(dist<best.dist)best={dist,i,j};
            }
        history.push({merged:[clusters[best.i],clusters[best.j]],dist:+best.dist.toFixed(3)});
        clusters=[...clusters.filter((_,k)=>k!==best.i&&k!==best.j),[...clusters[best.i],...clusters[best.j]]];
    }
    const labels=new Array(data.length).fill(0);
    clusters.forEach((cl,i)=>cl.forEach(idx=>labels[idx]=i));
    return{labels,history};
}

const data=[[1,1],[1,2],[2,1],[6,5],[5,6],[6,6]];
const{labels,history}=agglomerative(data,2);
console.log('Labels:',labels);  // [0,0,0,1,1,1]
console.log('Merge history:',history.map(h=>h.dist));
JS,
            ],
            [
                'title' => 'PCA Explained Variance Ratio', 'difficulty' => 'medium',
                'description' => 'Compute how much variance each principal component explains. Use a scree plot (or cumulative explained variance) to choose how many components to keep.',
                'solution_code' => <<<'JS'
const mm=(A,B)=>A.map(r=>B[0].map((_,j)=>r.reduce((s,_,k)=>s+r[k]*B[k][j],0)));
const T=M=>M[0].map((_,j)=>M.map(r=>r[j]));
const mvM=(M,v)=>M.map(r=>r.reduce((s,x,j)=>s+x*v[j],0));
const nor=v=>{const n=Math.sqrt(v.reduce((s,x)=>s+x*x,0));return v.map(x=>x/n);};
const ctr=X=>{const m=X[0].map((_,j)=>X.reduce((s,r)=>s+r[j],0)/X.length);return X.map(r=>r.map((x,j)=>x-m[j]));};
function eigenAll(cov,n){
    let mat=cov.map(r=>[...r]);const evs=[];
    for(let k=0;k<n;k++){
        let v=mat[0].map((_,j)=>j===0?1:0);
        for(let i=0;i<200;i++)v=nor(mvM(mat,v));
        const ev=v.reduce((s,vi,i)=>s+vi*mvM(mat,v)[i],0);
        evs.push(ev);
        mat=mat.map((r,ri)=>r.map((c,ci)=>c-ev*v[ri]*v[ci]));
    }
    return evs;
}

const X=[[2.5,2.4,1],[0.5,.7,3],[2.2,2.9,2],[1.9,2.2,1],[3.1,3,2],[2.3,2.7,1],[2,1.6,3],[1,1.1,2],[1.5,1.6,2],[1.1,.9,1]];
const Xc=ctr(X),cov=mm(T(Xc),Xc).map(r=>r.map(v=>v/Xc.length));
const evs=eigenAll(cov,3);
const total=evs.reduce((a,b)=>a+b,0);
const ratios=evs.map(e=>+(e/total*100).toFixed(1));
console.log('Explained variance %:',ratios);
console.log('Cumulative %:',ratios.reduce((acc,v,i)=>[...acc,(acc[i-1]||0)+v],[]));
JS,
            ],
            [
                'title' => 'Elbow Method for K-Means', 'difficulty' => 'medium',
                'description' => 'Compute within-cluster sum of squares (inertia) for k=1..maxK and plot the curve. The "elbow" — where inertia stops dropping sharply — suggests the optimal k.',
                'solution_code' => <<<'JS'
function inertia(data, centroids, labels) {
    return data.reduce((s,p,i)=>s+p.reduce((ss,x,j)=>ss+(x-centroids[labels[i]][j])**2,0),0);
}
function kMeansInertia(data,k){
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    let c=data.slice(0,k).map(p=>[...p]),labels=data.map(()=>0);
    for(let i=0;i<50;i++){
        const next=data.map(p=>c.reduce((b,ci,j)=>d(p,ci)<d(p,c[b])?j:b,0));
        c=c.map((_,j)=>{const cl=data.filter((_,i)=>next[i]===j);return cl.length?cl[0].map((_,d)=>cl.reduce((s,p)=>s+p[d],0)/cl.length):c[j];});
        if(JSON.stringify(next)===JSON.stringify(labels))break;
        labels=next;
    }
    return+inertia(data,c,labels).toFixed(2);
}

const data=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6],[10,10],[10,11],[11,10]];
console.log('k\tInertia');
for(let k=1;k<=5;k++) console.log(`${k}\t${kMeansInertia(data,k)}`);
// Large drop at k=1→2 and k=2→3, then levels off → elbow at k=3
JS,
            ],
            [
                'title' => 'Silhouette Score', 'difficulty' => 'medium',
                'description' => 'Measure how similar each point is to its own cluster versus the nearest other cluster. Scores near 1 indicate well-separated clusters; near 0 or negative indicates poor clustering.',
                'solution_code' => <<<'JS'
function silhouette(data, labels) {
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    const clusters=[...new Set(labels)];
    const scores=data.map((p,i)=>{
        const myCluster=labels[i];
        const intraPoints=data.filter((_,j)=>j!==i&&labels[j]===myCluster);
        const a=intraPoints.length?intraPoints.reduce((s,q)=>s+d(p,q),0)/intraPoints.length:0;
        const b=Math.min(...clusters.filter(c=>c!==myCluster).map(c=>{
            const pts=data.filter((_,j)=>labels[j]===c);
            return pts.reduce((s,q)=>s+d(p,q),0)/pts.length;
        }));
        return(b-a)/Math.max(a,b);
    });
    return{scores:scores.map(s=>+s.toFixed(4)),mean:+(scores.reduce((a,b)=>a+b,0)/scores.length).toFixed(4)};
}

const data=[[1,1],[1,2],[2,1],[5,5],[6,5],[5,6]];
const labels=[0,0,0,1,1,1];
const r=silhouette(data,labels);
console.log('Score per point:',r.scores);
console.log('Mean silhouette:',r.mean);  // should be high (well-separated)
JS,
            ],
            [
                'title' => 'Z-Score Outlier Detection', 'difficulty' => 'medium',
                'description' => 'Flag data points with |z-score| > threshold as outliers. Assumes normally distributed data. Points more than 3 standard deviations from the mean are outliers in most datasets.',
                'solution_code' => <<<'JS'
function zScoreOutliers(data, col, threshold=3) {
    const vals=data.map(r=>r[col]);
    const n=vals.length, mean=vals.reduce((a,b)=>a+b,0)/n;
    const std=Math.sqrt(vals.reduce((s,x)=>s+(x-mean)**2,0)/n);
    return data.map(r=>({
        ...r,
        z:+((r[col]-mean)/std).toFixed(4),
        isOutlier:Math.abs((r[col]-mean)/std)>threshold,
    }));
}

const data=[
    {id:1,salary:50},{id:2,salary:55},{id:3,salary:52},{id:4,salary:48},
    {id:5,salary:200},{id:6,salary:53},{id:7,salary:51},{id:8,salary:49},
];
const result=zScoreOutliers(data,'salary');
result.forEach(r=>console.log(`id=${r.id} salary=${r.salary} z=${r.z}${r.isOutlier?' ← OUTLIER':''}`));
JS,
            ],
            [
                'title' => 'IQR Anomaly Detection', 'difficulty' => 'medium',
                'description' => 'Detect anomalies using the interquartile range: flag points below Q1−1.5·IQR or above Q3+1.5·IQR as outliers. More robust than z-score for skewed distributions.',
                'solution_code' => <<<'JS'
function iqrOutliers(data, col) {
    const vals=[...data.map(r=>r[col])].sort((a,b)=>a-b);
    const n=vals.length;
    const q1=vals[Math.floor(n*0.25)], q3=vals[Math.floor(n*0.75)];
    const iqr=q3-q1;
    const lo=q1-1.5*iqr, hi=q3+1.5*iqr;
    const flagged=data.filter(r=>r[col]<lo||r[col]>hi);
    return{q1,q3,iqr:+iqr.toFixed(2),lowerFence:+lo.toFixed(2),upperFence:+hi.toFixed(2),outliers:flagged,clean:data.filter(r=>r[col]>=lo&&r[col]<=hi)};
}

const data=[1,2,3,4,5,6,7,8,100,-50].map((v,i)=>({id:i,value:v}));
const r=iqrOutliers(data,'value');
console.log(`IQR=${r.iqr}, fences=[${r.lowerFence}, ${r.upperFence}]`);
console.log('Outliers:',r.outliers.map(o=>o.value));  // [100,-50]
JS,
            ],
            [
                'title' => 'Local Outlier Factor (LOF)', 'difficulty' => 'medium',
                'description' => 'LOF measures local density deviation: a point much less dense than its neighbors gets a high outlier score. Unlike global methods, LOF detects outliers relative to local context.',
                'solution_code' => <<<'JS'
function lof(data, k=3) {
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    const knn=i=>data.map((p,j)=>({j,d:d(data[i],p)})).sort((a,b)=>a.d-b.d).slice(1,k+1);
    const kDist=i=>knn(i)[k-1].d;
    const rd=(i,j)=>Math.max(kDist(j),d(data[i],data[j]));
    const lrd=i=>{const nb=knn(i);return nb.length/nb.reduce((s,{j})=>s+rd(i,j),0);};
    return data.map((_,i)=>{
        const nb=knn(i);
        const score=nb.reduce((s,{j})=>s+lrd(j),0)/(nb.length*lrd(i));
        return{i,lof:+score.toFixed(4)};
    });
}

const data=[[1,1],[1.2,1],[1,1.2],[5,5],[5.2,5],[5,5.2],[10,10]];  // last point is outlier
const scores=lof(data,3);
scores.forEach(({i,lof})=>console.log(`point ${i} ${JSON.stringify(data[i])}: LOF=${lof}`));
// point near [10,10] should have LOF >> 1
JS,
            ],
            [
                'title' => 'Simple Moving Average', 'difficulty' => 'medium',
                'description' => 'Compute the simple moving average (SMA) of a time series with a sliding window of size w. Each output value is the mean of the previous w data points.',
                'solution_code' => <<<'JS'
function sma(series, w) {
    const result=[];
    for(let i=w-1;i<series.length;i++){
        const window=series.slice(i-w+1,i+1);
        result.push({t:i,value:+(window.reduce((a,b)=>a+b,0)/w).toFixed(3)});
    }
    return result;
}

function ema(series, alpha) {
    const result=[series[0]];
    for(let i=1;i<series.length;i++) result.push(+(alpha*series[i]+(1-alpha)*result[i-1]).toFixed(3));
    return result;
}

const price=[100,102,101,103,105,104,107,109,108,110,112];
console.log('SMA(3):',sma(price,3).map(r=>r.value));
console.log('EMA(α=0.3):',ema(price,0.3));
JS,
            ],
            [
                'title' => 'Weighted Moving Average Forecast', 'difficulty' => 'medium',
                'description' => 'Predict the next value in a time series by computing a linearly weighted average of the last w observations, giving more weight to recent values.',
                'solution_code' => <<<'JS'
function wma(series, w) {
    const weights=Array.from({length:w},(_,i)=>i+1);
    const total=weights.reduce((a,b)=>a+b,0);
    const result=[];
    for(let i=w-1;i<series.length;i++){
        const window=series.slice(i-w+1,i+1);
        const val=window.reduce((s,v,j)=>s+v*weights[j],0)/total;
        result.push(+val.toFixed(3));
    }
    return result;
}

function forecastNext(series, w=3) {
    const weights=Array.from({length:w},(_,i)=>i+1);
    const total=weights.reduce((a,b)=>a+b,0);
    const window=series.slice(-w);
    return +(window.reduce((s,v,j)=>s+v*weights[j],0)/total).toFixed(3);
}

const series=[10,11,13,12,14,15,16,14,17,18];
console.log('WMA(3):',wma(series,3));
console.log('Next forecast:',forecastNext(series,3));
JS,
            ],
            [
                'title' => 'Linear Trend Detection', 'difficulty' => 'medium',
                'description' => 'Fit a linear regression line to a time series (index vs value) to detect upward or downward trends. Slope > 0 means upward trend, slope < 0 means downward.',
                'solution_code' => <<<'JS'
function trendLine(series) {
    const n=series.length;
    const xs=Array.from({length:n},(_,i)=>i);
    const mx=xs.reduce((a,b)=>a+b,0)/n, my=series.reduce((a,b)=>a+b,0)/n;
    const slope=xs.reduce((s,x,i)=>s+(x-mx)*(series[i]-my),0)/xs.reduce((s,x)=>s+(x-mx)**2,0);
    const intercept=my-slope*mx;
    const trend=xs.map(x=>+(slope*x+intercept).toFixed(3));
    const direction=slope>0.1?'Upward':slope<-0.1?'Downward':'Stationary';
    return{slope:+slope.toFixed(4),intercept:+intercept.toFixed(4),trend,direction};
}

const upward  =[10,12,11,13,15,14,16,18,17,20];
const downward=[20,18,19,17,16,15,14,12,13,10];
console.log(trendLine(upward).direction,  'slope:', trendLine(upward).slope);
console.log(trendLine(downward).direction,'slope:', trendLine(downward).slope);
JS,
            ],
            [
                'title' => 'Seasonal Decomposition (Additive)', 'difficulty' => 'medium',
                'description' => 'Decompose a time series into Trend + Seasonal + Residual components assuming an additive model: observed = trend + seasonal + residual.',
                'solution_code' => <<<'JS'
function decompose(series, period) {
    const n=series.length;
    // Trend: centered moving average
    const trend=series.map((_,i)=>{
        if(i<period/2||i>=n-period/2)return null;
        const w=series.slice(Math.round(i-period/2),Math.round(i+period/2));
        return+(w.reduce((a,b)=>a+b,0)/w.length).toFixed(3);
    });
    // Seasonal: average deviation from trend per period position
    const seasonal=new Array(n).fill(0);
    const seasonalAvg=Array.from({length:period},(_,s)=>{
        const vals=series.filter((_,i)=>trend[i]!=null&&i%period===s).map((v,j,arr)=>v-trend[series.findIndex((_,i)=>trend[i]!=null&&i%period===s)+j*period]);
        return vals.length?vals.reduce((a,b)=>a+b,0)/vals.length:0;
    });
    series.forEach((_,i)=>seasonal[i]=+seasonalAvg[i%period].toFixed(3));
    const residual=series.map((v,i)=>trend[i]!=null?+(v-trend[i]-seasonal[i]).toFixed(3):null);
    return{trend,seasonal,residual};
}

const series=[10,14,12,8,11,15,13,9,12,16,14,10];  // period=4
const{trend,seasonal,residual}=decompose(series,4);
console.log('Trend:',trend);
console.log('Seasonal:',seasonal.slice(0,4));
console.log('Residual:',residual);
JS,
            ],
            [
                'title' => 'Autoregressive AR(p) Model', 'difficulty' => 'medium',
                'description' => 'Fit an AR(p) model: predict the next value as a linear combination of the previous p values. Use OLS to estimate coefficients from the series history.',
                'solution_code' => <<<'JS'
function arModel(series, p) {
    // Build design matrix: X[i] = [y[i-1], y[i-2], ..., y[i-p]], y[i]
    const X=[],y=[];
    for(let i=p;i<series.length;i++){
        X.push(series.slice(i-p,i).reverse());
        y.push(series[i]);
    }
    // OLS: w = (X^T X)^-1 X^T y (simplified for p=1,2)
    // Gradient descent for general p
    let w=new Array(p).fill(0),b=0,lr=0.01;
    for(let e=0;e<2000;e++){
        const dw=new Array(p).fill(0);let db=0;
        X.forEach((x,j)=>{const err=x.reduce((s,xi,i)=>s+xi*w[i],b)-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        w=w.map((wi,i)=>wi-lr*dw[i]/X.length);b-=lr*db/X.length;
    }
    const forecast=h=>{
        const hist=[...series];
        for(let i=0;i<h;i++){const x=hist.slice(-p).reverse();hist.push(x.reduce((s,xi,i)=>s+xi*w[i],b));}
        return hist.slice(-h).map(v=>+v.toFixed(2));
    };
    return{w:w.map(v=>+v.toFixed(4)),b:+b.toFixed(4),forecast};
}

const series=[10,11,12,13,14,15,16,17,18,19,20];
const model=arModel(series,2);
console.log('Coefficients:',model.w,'Bias:',model.b);
console.log('Forecast next 3:',model.forecast(3));  // ≈ [21, 22, 23]
JS,
            ],
            [
                'title' => 'Rolling Statistics', 'difficulty' => 'medium',
                'description' => 'Compute rolling mean and rolling standard deviation over a sliding window. Equivalent to df[col].rolling(w).mean() in pandas. Useful for visualizing trend and volatility.',
                'solution_code' => <<<'JS'
function rolling(series, w) {
    return series.map((_, i) => {
        if (i < w-1) return {mean:null, std:null};
        const window = series.slice(i-w+1, i+1);
        const mean = window.reduce((a,b)=>a+b,0)/w;
        const std  = Math.sqrt(window.reduce((s,x)=>s+(x-mean)**2,0)/w);
        return {mean:+mean.toFixed(3), std:+std.toFixed(3)};
    });
}

const prices=[100,102,98,105,103,108,101,107,110,106,112,109];
const stats=rolling(prices,4);
console.log('t  price  roll-mean  roll-std');
prices.forEach((p,i)=>console.log(`${i}    ${p}    ${stats[i].mean??'N/A'}    ${stats[i].std??'N/A'}`));
JS,
            ],
            [
                'title' => 'Lag Feature Engineering', 'difficulty' => 'medium',
                'description' => 'Create lag features for time series: shift the series by 1, 2, …, k time steps so past values become input features for supervised learning.',
                'solution_code' => <<<'JS'
function lagFeatures(series, lags=[1,2,3]) {
    return series.map((v, i) => {
        const row = {t: i, y: v};
        lags.forEach(lag => row[`lag_${lag}`] = i >= lag ? series[i-lag] : null);
        return row;
    }).filter(r => lags.every(l => r[`lag_${l}`] !== null));
}

const prices=[100,102,105,103,108,110,107,112,115,113];
const features=lagFeatures(prices,[1,2,3]);
console.log('Features with lags:');
features.slice(0,4).forEach(r=>console.log(r));
// y=103, lag_1=105, lag_2=102, lag_3=100
// These become X and y for a supervised regression
JS,
            ],
            [
                'title' => 'Time Series Train/Test Split', 'difficulty' => 'medium',
                'description' => 'Split time series data chronologically — no shuffling allowed. The last n% of timesteps become the test set to evaluate how the model generalizes to unseen future data.',
                'solution_code' => <<<'JS'
function timeSeriesSplit(series, testRatio=0.2) {
    const n = series.length;
    const splitIdx = Math.floor(n * (1 - testRatio));
    return {train: series.slice(0, splitIdx), test: series.slice(splitIdx), splitAt: splitIdx};
}

function walkForwardCV(series, nFolds=5, minTrainSize=10) {
    const n = series.length, foldSize = Math.floor((n-minTrainSize)/nFolds);
    return Array.from({length:nFolds}, (_,i) => ({
        trainEnd: minTrainSize + i*foldSize,
        testStart: minTrainSize + i*foldSize,
        testEnd: Math.min(minTrainSize + (i+1)*foldSize, n),
    }));
}

const series=Array.from({length:50},(_,i)=>Math.sin(i*0.3)*10+i+Math.random());
const {train,test}=timeSeriesSplit(series);
console.log(`Train: t0–t${train.length-1} (${train.length} points)`);
console.log(`Test:  t${train.length}–t${series.length-1} (${test.length} points)`);
const folds=walkForwardCV(series);
console.log('Walk-forward folds:',folds.slice(0,3));
JS,
            ],
            [
                'title' => 'Forecast Evaluation: MAE and RMSE', 'difficulty' => 'medium',
                'description' => 'Compute Mean Absolute Error (MAE) and Root Mean Squared Error (RMSE) to evaluate time series forecasts. MAE is more interpretable; RMSE penalizes large errors more heavily.',
                'solution_code' => <<<'JS'
function forecastMetrics(actual, predicted) {
    const n=actual.length;
    const mae  = actual.reduce((s,y,i)=>s+Math.abs(y-predicted[i]),0)/n;
    const mse  = actual.reduce((s,y,i)=>s+(y-predicted[i])**2,0)/n;
    const mape = actual.reduce((s,y,i)=>s+Math.abs((y-predicted[i])/y),0)/n*100;
    return{mae:+mae.toFixed(4),rmse:+Math.sqrt(mse).toFixed(4),mape:+mape.toFixed(2)};
}

// Compare two forecasting methods
const actual    =[100,102,105,103,108,110,107,112];
const forecast1 =[101,103,104,104,107,111,108,111];  // simple MA
const forecast2 =[99, 101,106,102,109,109,108,113];  // more volatile

console.log('Method 1:', forecastMetrics(actual,forecast1));
console.log('Method 2:', forecastMetrics(actual,forecast2));
JS,
            ],
            [
                'title' => 'Stationarity Test (Simplified ADF)', 'difficulty' => 'medium',
                'description' => 'Test whether a time series is stationary (mean and variance do not change over time) using a simplified version of the Augmented Dickey-Fuller test based on variance ratios.',
                'solution_code' => <<<'JS'
function stationarityTest(series) {
    const n=series.length, half=Math.floor(n/2);
    const first=series.slice(0,half), second=series.slice(half);
    const mean=arr=>arr.reduce((a,b)=>a+b,0)/arr.length;
    const variance=arr=>{const m=mean(arr);return arr.reduce((s,x)=>s+(x-m)**2,0)/arr.length;};
    const m1=mean(first),m2=mean(second),v1=variance(first),v2=variance(second);
    const meanDiff=Math.abs(m1-m2), varRatio=Math.max(v1,v2)/(Math.min(v1,v2)||1);
    const isStationary=meanDiff<5&&varRatio<2;
    // First difference to make stationary
    const diff=series.slice(1).map((v,i)=>v-series[i]);
    return{mean:[+m1.toFixed(2),+m2.toFixed(2)],variance:[+v1.toFixed(2),+v2.toFixed(2)],varRatio:+varRatio.toFixed(3),isStationary,diff:diff.slice(0,5)};
}

const stationary=Array.from({length:20},()=>Math.random()*2+5);
const nonStat=Array.from({length:20},(_,i)=>i*2+Math.random()*3);
console.log('Stationary series:', stationarityTest(stationary).isStationary);    // true
console.log('Non-stationary:',    stationarityTest(nonStat).isStationary);       // false
console.log('After diff:', stationarityTest(stationarityTest(nonStat).diff).isStationary); // true
JS,
            ],
            [
                'title' => 'MDS: Multidimensional Scaling', 'difficulty' => 'medium',
                'description' => 'Reduce high-dimensional data to 2D by preserving pairwise distances. Classical MDS (cMDS) applies eigen-decomposition on the doubly-centered distance matrix.',
                'solution_code' => <<<'JS'
function mds(data, dims=2) {
    const n=data.length;
    const d=(a,b)=>Math.sqrt(a.reduce((s,x,i)=>s+(x-b[i])**2,0));
    const D=data.map(a=>data.map(b=>d(a,b)**2));
    // Double-center
    const rowMean=D.map(r=>r.reduce((a,b)=>a+b,0)/n);
    const colMean=D[0].map((_,j)=>D.reduce((s,r)=>s+r[j],0)/n);
    const total=D.reduce((s,r)=>s+r.reduce((a,b)=>a+b,0),0)/(n*n);
    const B=D.map((r,i)=>r.map((v,j)=>-0.5*(v-rowMean[i]-colMean[j]+total)));
    // Power iteration for top dims eigenvectors
    const proj=Array.from({length:dims},(_,k)=>{
        let v=new Array(n).fill(0);v[k]=1;
        for(let i=0;i<200;i++){const nv=B.map(r=>r.reduce((s,b,j)=>s+b*v[j],0));const nm=Math.sqrt(nv.reduce((s,x)=>s+x*x,0));v=nv.map(x=>x/nm);}
        const ev=B.map(r=>r.reduce((s,b,j)=>s+b*v[j],0)).reduce((s,x,i)=>s+x*v[i],0);
        return{v,ev:Math.sqrt(Math.abs(ev))};
    });
    return data.map((_,i)=>proj.map(({v,ev})=>+(v[i]*ev).toFixed(4)));
}

const data=[[1,0,0],[0,1,0],[0,0,1],[5,5,0],[5,0,5],[0,5,5]];
const coords=mds(data,2);
console.log('2D projections:');
coords.forEach((c,i)=>console.log(`point ${i}: [${c}]`));
// Similar points should be close in 2D
JS,
            ],
            [
                'title' => '2D Gaussian Mixture Model (EM)', 'difficulty' => 'medium',
                'description' => 'Fit a Gaussian Mixture Model with 2 components to 2D data using the Expectation-Maximization algorithm. Alternates between soft assignments (E-step) and parameter updates (M-step).',
                'solution_code' => <<<'JS'
function gmm2D(data, K=2, maxIter=50) {
    const n=data.length;
    // Init: random means, unit covariance, equal weights
    let means=data.slice(0,K).map(p=>[...p]);
    let covs=Array.from({length:K},()=>[[1,0],[0,1]]);
    let weights=new Array(K).fill(1/K);
    const gauss2D=([x,y],[mx,my],[[a,b],[c,d]])=>{const det=a*d-b*c||1e-8,dx=x-mx,dy=y-my;const e=-(dx*(d*dx-b*dy)+dy*(a*dy-c*dx))/(2*det);return Math.exp(e)/(2*Math.PI*Math.sqrt(Math.abs(det)));};
    for(let iter=0;iter<maxIter;iter++){
        // E-step
        const r=data.map(p=>weights.map((w,k)=>w*gauss2D(p,means[k],covs[k]))).map(row=>{const s=row.reduce((a,b)=>a+b,0)||1e-15;return row.map(v=>v/s);});
        // M-step
        const Nk=Array.from({length:K},(_,k)=>r.reduce((s,ri)=>s+ri[k],0)||1e-8);
        means=Array.from({length:K},(_,k)=>data.reduce((s,p,i)=>[s[0]+r[i][k]*p[0],s[1]+r[i][k]*p[1]],[0,0]).map(v=>v/Nk[k]));
        covs=Array.from({length:K},(_,k)=>data.reduce((s,p,i)=>{const dx=p[0]-means[k][0],dy=p[1]-means[k][1],ri=r[i][k];return[[s[0][0]+ri*dx*dx,s[0][1]+ri*dx*dy],[s[1][0]+ri*dx*dy,s[1][1]+ri*dy*dy]];},[[0,0],[0,0]]).map(row=>row.map(v=>v/Nk[k])));
        weights=Nk.map(nk=>nk/n);
    }
    const labels=data.map(p=>weights.map((w,k)=>w*gauss2D(p,means[k],covs[k])).reduce((bi,v,k,arr)=>v>arr[bi]?k:bi,0));
    return{means:means.map(m=>m.map(v=>+v.toFixed(3))),weights:weights.map(v=>+v.toFixed(3)),labels};
}

const cluster1=Array.from({length:10},()=>[1+Math.random(),1+Math.random()]);
const cluster2=Array.from({length:10},()=>[6+Math.random(),6+Math.random()]);
const r=gmm2D([...cluster1,...cluster2],2);
console.log('Means:',r.means);
console.log('Weights:',r.weights);
console.log('Labels:',r.labels);  // first 10 should be 0 or 1, last 10 opposite
JS,
            ],
        ];
    }

    // ─── ADVANCED / HARD (501–530) ───────────────────────────────────────────

    private function advancedProblems(): array
    {
        return [
            [
                'title' => '3-Layer Neural Network with Training Loop', 'difficulty' => 'hard',
                'description' => 'Build and train a 3-layer feedforward neural network (input → hidden1 → hidden2 → output) with ReLU activations and sigmoid output using mini-batch gradient descent.',
                'solution_code' => <<<'JS'
const relu=x=>Math.max(0,x), reluD=x=>x>0?1:0, sig=x=>1/(1+Math.exp(-x));
function initNet(d){return{W1:Array.from({length:d[1]},()=>Array.from({length:d[0]},()=>(Math.random()-.5)*.5)),b1:new Array(d[1]).fill(0),W2:Array.from({length:d[2]},()=>Array.from({length:d[1]},()=>(Math.random()-.5)*.5)),b2:new Array(d[2]).fill(0),W3:Array.from({length:d[3]},()=>Array.from({length:d[2]},()=>(Math.random()-.5)*.5)),b3:new Array(d[3]).fill(0)};}
function fwd(net,x){const mm=(W,v)=>W.map(row=>row.reduce((s,w,i)=>s+w*v[i],0));const a1=mm(net.W1,x).map((z,i)=>relu(z+net.b1[i]));const a2=mm(net.W2,a1).map((z,i)=>relu(z+net.b2[i]));const z3=mm(net.W3,a2).map((z,i)=>z+net.b3[i]);const a3=z3.map(sig);return{a1,a2,a3,x};}
function bwd(net,cache,y,lr=0.1){const{a1,a2,a3,x}=cache;const dz3=a3.map((o,i)=>o-y[i]);net.W3=net.W3.map((row,i)=>row.map((w,j)=>w-lr*dz3[i]*a2[j]));net.b3=net.b3.map((b,i)=>b-lr*dz3[i]);const da2=net.W3[0].map((_,j)=>dz3.reduce((s,d,i)=>s+d*net.W3[i][j],0));const dz2=da2.map((d,i)=>d*reluD(a2[i]));net.W2=net.W2.map((row,i)=>row.map((w,j)=>w-lr*dz2[i]*a1[j]));net.b2=net.b2.map((b,i)=>b-lr*dz2[i]);const da1=net.W2[0].map((_,j)=>dz2.reduce((s,d,i)=>s+d*net.W2[i][j],0));const dz1=da1.map((d,i)=>d*reluD(a1[i]));net.W1=net.W1.map((row,i)=>row.map((w,j)=>w-lr*dz1[i]*x[j]));net.b1=net.b1.map((b,i)=>b-lr*dz1[i]);}

const net=initNet([2,4,4,1]);
const X=[[0,0],[0,1],[1,0],[1,1]],y=[[0],[1],[1],[0]];  // XOR
for(let e=0;e<3000;e++) X.forEach((x,i)=>{const c=fwd(net,x);bwd(net,c,y[i],0.5);});
console.log('XOR predictions:');
X.forEach((x,i)=>console.log(`[${x}] → ${fwd(net,x).a3[0].toFixed(3)} (expected ${y[i][0]})`));
JS,
            ],
            [
                'title' => 'SGD with Momentum', 'difficulty' => 'hard',
                'description' => 'Implement stochastic gradient descent with momentum. Momentum accumulates a velocity vector in directions of persistent gradient, dampening oscillations and accelerating convergence.',
                'solution_code' => <<<'JS'
function sgdMomentum(X, y, lr=0.1, momentum=0.9, epochs=500) {
    const sig=z=>1/(1+Math.exp(-z));
    let w=new Array(X[0].length).fill(0),b=0;
    let vw=new Array(X[0].length).fill(0),vb=0;
    const history=[];
    for(let e=0;e<epochs;e++){
        const idx=[...X.keys()].sort(()=>Math.random()-.5);
        let totalLoss=0;
        idx.forEach(i=>{
            const p=sig(X[i].reduce((s,xi,j)=>s+xi*w[j],b));
            const err=p-y[i];
            const gw=X[i].map(xi=>err*xi), gb=err;
            vw=vw.map((v,j)=>momentum*v+lr*gw[j]);
            vb=momentum*vb+lr*gb;
            w=w.map((wi,j)=>wi-vw[j]); b-=vb;
            totalLoss-=y[i]*Math.log(p+1e-15)+(1-y[i])*Math.log(1-p+1e-15);
        });
        if(e%100===0)history.push({epoch:e,loss:+(totalLoss/X.length).toFixed(4)});
    }
    const acc=X.filter((x,i)=>(sig(x.reduce((s,xi,j)=>s+xi*w[j],b))>=0.5?1:0)===y[i]).length/X.length;
    return{w:w.map(v=>+v.toFixed(4)),acc:+acc.toFixed(4),history};
}

const X=[[1,2],[2,3],[3,1],[6,5],[5,7],[7,6]],y=[0,0,0,1,1,1];
const r=sgdMomentum(X,y);
console.log('Accuracy:',r.acc);
console.log('Loss history:',r.history);
JS,
            ],
            [
                'title' => 'Mini-Batch Training Loop', 'difficulty' => 'hard',
                'description' => 'Implement a mini-batch gradient descent training loop that processes data in batches of size B, computes gradients on each batch, and updates weights. Tracks loss curve across epochs.',
                'solution_code' => <<<'JS'
function miniBatchTrain(X, y, batchSize=4, lr=0.05, epochs=200) {
    const sig=z=>1/(1+Math.exp(-z));
    let w=new Array(X[0].length).fill(0),b=0;
    const lossCurve=[];
    for(let e=0;e<epochs;e++){
        const order=[...X.keys()].sort(()=>Math.random()-.5);
        let epochLoss=0;
        for(let start=0;start<order.length;start+=batchSize){
            const batch=order.slice(start,start+batchSize);
            const dw=new Array(w.length).fill(0);let db=0;
            batch.forEach(i=>{
                const p=sig(X[i].reduce((s,xi,j)=>s+xi*w[j],b));
                const err=p-y[i];
                X[i].forEach((xi,j)=>dw[j]+=err*xi);
                db+=err;
                epochLoss-=y[i]*Math.log(p+1e-15)+(1-y[i])*Math.log(1-p+1e-15);
            });
            w=w.map((wi,j)=>wi-lr*dw[j]/batch.length);
            b-=lr*db/batch.length;
        }
        if(e%50===0)lossCurve.push({epoch:e,loss:+(epochLoss/X.length).toFixed(4)});
    }
    const acc=X.filter((x,i)=>(sig(x.reduce((s,xi,j)=>s+xi*w[j],b))>=0.5?1:0)===y[i]).length/X.length;
    return{w,b,acc:+acc.toFixed(4),lossCurve};
}

const X=[[1,2],[2,3],[3,1],[6,5],[5,7],[7,6],[2,1],[6,7]],y=[0,0,0,1,1,1,0,1];
const r=miniBatchTrain(X,y);
console.log('Accuracy:',r.acc);
console.log('Loss curve:',r.lossCurve);
JS,
            ],
            [
                'title' => 'Max Pooling and Average Pooling', 'difficulty' => 'hard',
                'description' => 'Implement 2D max pooling and average pooling for CNN feature maps. Reduce spatial dimensions by taking the max (or mean) in each pooling window.',
                'solution_code' => <<<'JS'
function maxPool(featureMap, poolSize=2, stride=2) {
    const [h,w]=[ featureMap.length, featureMap[0].length ];
    const outH=Math.floor((h-poolSize)/stride)+1;
    const outW=Math.floor((w-poolSize)/stride)+1;
    return Array.from({length:outH},(_,i)=>Array.from({length:outW},(_,j)=>{
        let max=-Infinity;
        for(let pi=0;pi<poolSize;pi++) for(let pj=0;pj<poolSize;pj++) max=Math.max(max,featureMap[i*stride+pi][j*stride+pj]);
        return max;
    }));
}

function avgPool(featureMap, poolSize=2, stride=2) {
    const [h,w]=[ featureMap.length, featureMap[0].length ];
    const outH=Math.floor((h-poolSize)/stride)+1;
    const outW=Math.floor((w-poolSize)/stride)+1;
    return Array.from({length:outH},(_,i)=>Array.from({length:outW},(_,j)=>{
        let sum=0;
        for(let pi=0;pi<poolSize;pi++) for(let pj=0;pj<poolSize;pj++) sum+=featureMap[i*stride+pi][j*stride+pj];
        return+(sum/(poolSize*poolSize)).toFixed(3);
    }));
}

const fm=[[1,3,2,4],[5,6,7,8],[3,2,1,0],[9,1,2,3]];
console.log('Max Pool 2x2:', maxPool(fm,2,2));   // [[6,8],[9,3]]
console.log('Avg Pool 2x2:', avgPool(fm,2,2));   // [[3.75,5.25],[3.75,1.5]]
JS,
            ],
            [
                'title' => 'RNN Cell Forward Pass', 'difficulty' => 'hard',
                'description' => 'Implement a vanilla RNN cell: h_t = tanh(W_h · h_{t-1} + W_x · x_t + b). Process a sequence step by step, carrying hidden state forward across timesteps.',
                'solution_code' => <<<'JS'
function initRNN(inputSize, hiddenSize) {
    const rand=()=>(Math.random()-.5)*0.1;
    return{
        Wh:Array.from({length:hiddenSize},()=>Array.from({length:hiddenSize},rand)),
        Wx:Array.from({length:hiddenSize},()=>Array.from({length:inputSize},rand)),
        b:new Array(hiddenSize).fill(0),
        hiddenSize,
    };
}

function rnnForward(cell, sequence, h0=null) {
    let h=h0||new Array(cell.hiddenSize).fill(0);
    const hidden=[h];
    for(const x of sequence){
        const z=cell.Wh.map((row,i)=>row.reduce((s,w,j)=>s+w*h[j],0)+cell.Wx[i].reduce((s,w,j)=>s+w*x[j],0)+cell.b[i]);
        h=z.map(Math.tanh);
        hidden.push(h);
    }
    return{hiddenStates:hidden,final:h};
}

const cell=initRNN(3,4);
const seq=[[1,0,0],[0,1,0],[0,0,1],[1,1,0]];
const{hiddenStates,final}=rnnForward(cell,seq);
console.log('Steps processed:',hiddenStates.length-1);
console.log('Final hidden state:',final.map(v=>+v.toFixed(4)));
JS,
            ],
            [
                'title' => 'GRU Cell Forward Pass', 'difficulty' => 'hard',
                'description' => 'Implement a Gated Recurrent Unit cell with reset gate r, update gate z, and candidate hidden state h̃. GRUs use fewer parameters than LSTMs while handling long-term dependencies.',
                'solution_code' => <<<'JS'
const sig=x=>1/(1+Math.exp(-x));
function gruCell(x, h, Wz, Wr, Wh, bz, br, bh) {
    const dot=(W,v)=>W.map(row=>row.reduce((s,w,i)=>s+w*(v[i]||0),0));
    const add=(a,b)=>a.map((v,i)=>v+b[i]);
    const nx=x.concat(h);
    const z=add(dot(Wz,nx),bz).map(sig);
    const r=add(dot(Wr,nx),br).map(sig);
    const hcIn=x.concat(r.map((ri,i)=>ri*h[i]));
    const hc=add(dot(Wh,hcIn),bh).map(Math.tanh);
    return h.map((hi,i)=>(1-z[i])*hi+z[i]*hc[i]);
}

function initGRU(inputSize, hiddenSize) {
    const rand=n=>Array.from({length:n},()=>(Math.random()-.5)*0.1);
    const W=n=>Array.from({length:hiddenSize},()=>rand(n));
    const b=()=>new Array(hiddenSize).fill(0);
    return{Wz:W(inputSize+hiddenSize),Wr:W(inputSize+hiddenSize),Wh:W(inputSize+hiddenSize),bz:b(),br:b(),bh:b(),hiddenSize};
}

const gru=initGRU(3,4);
let h=new Array(4).fill(0);
const seq=[[1,0,0],[0,1,0],[0,0,1]];
for(const x of seq) h=gruCell(x,h,gru.Wz,gru.Wr,gru.Wh,gru.bz,gru.br,gru.bh);
console.log('Final GRU hidden state:',h.map(v=>+v.toFixed(4)));
JS,
            ],
            [
                'title' => 'Character-Level Language Model', 'difficulty' => 'hard',
                'description' => 'Train a simple character-level bigram language model from a text corpus. Estimate transition probabilities P(char_t | char_{t-1}) and sample new text character by character.',
                'solution_code' => <<<'JS'
function charLM(text) {
    const chars=[...new Set(text)].sort();
    const idx=Object.fromEntries(chars.map((c,i)=>[c,i]));
    const n=chars.length;
    const counts=Array.from({length:n},()=>new Array(n).fill(1));
    for(let i=0;i<text.length-1;i++) counts[idx[text[i]]][idx[text[i+1]]]++;
    const probs=counts.map(row=>{const s=row.reduce((a,b)=>a+b,0);return row.map(c=>c/s);});

    function sample(prevChar, temp=1.0) {
        const logits=probs[idx[prevChar]].map(p=>Math.log(p)/temp);
        const max=Math.max(...logits);
        const exp=logits.map(l=>Math.exp(l-max));
        const sum=exp.reduce((a,b)=>a+b,0);
        const softmax=exp.map(e=>e/sum);
        let r=Math.random(),cum=0;
        for(let i=0;i<n;i++){cum+=softmax[i];if(r<cum)return chars[i];}
        return chars[n-1];
    }

    function generate(startChar, length=50, temp=0.8) {
        let out=startChar;
        for(let i=0;i<length-1;i++) out+=sample(out[out.length-1],temp);
        return out;
    }
    return{generate,chars,probs};
}

const corpus='the quick brown fox jumps over the lazy dog. the dog barked at the fox.';
const lm=charLM(corpus);
console.log('Generated:', lm.generate('t',40));
JS,
            ],
            [
                'title' => 'Layer Normalization', 'difficulty' => 'hard',
                'description' => 'Implement layer normalization: normalize across the feature dimension of each sample independently, then apply learnable scale (γ) and shift (β). Used in transformers and RNNs.',
                'solution_code' => <<<'JS'
function layerNorm(x, gamma, beta, eps=1e-8) {
    const mean = x.reduce((a,b)=>a+b,0)/x.length;
    const variance = x.reduce((s,xi)=>s+(xi-mean)**2,0)/x.length;
    const norm = x.map(xi=>(xi-mean)/Math.sqrt(variance+eps));
    return norm.map((xi,i)=>gamma[i]*xi+beta[i]);
}

function layerNormBatch(X, gamma, beta, eps=1e-8) {
    return X.map(x=>layerNorm(x,gamma,beta,eps));
}

const X=[
    [0.5, 1.2, -0.3, 2.1],
    [1.1, -0.5, 0.8, 0.2],
    [-1.0, 0.3, 1.5, -0.8],
];
const gamma=new Array(4).fill(1.0);
const beta =new Array(4).fill(0.0);
const normed=layerNormBatch(X,gamma,beta);
console.log('After LayerNorm:');
normed.forEach((row,i)=>{
    const mean=row.reduce((a,b)=>a+b,0)/row.length;
    const std=Math.sqrt(row.reduce((s,x)=>s+(x-mean)**2,0)/row.length);
    console.log(`row ${i}: mean≈${mean.toFixed(6)}, std≈${std.toFixed(4)}`);
});
JS,
            ],
            [
                'title' => 'Cosine Learning Rate Scheduler', 'difficulty' => 'hard',
                'description' => 'Implement a cosine annealing learning rate schedule with warm restarts (SGDR). The LR decays from max to min following a cosine curve, then restarts. Helps escape local minima.',
                'solution_code' => <<<'JS'
function cosineScheduler(lrMax=0.1, lrMin=0.001, T0=50, Tmult=2) {
    let epoch=0, cycleStart=0, cycleLen=T0;
    return function next() {
        const t=epoch-cycleStart;
        const lr=lrMin+(lrMax-lrMin)*0.5*(1+Math.cos(Math.PI*t/cycleLen));
        epoch++;
        if(t+1>=cycleLen){cycleStart=epoch;cycleLen*=Tmult;}
        return+lr.toFixed(6);
    };
}

const schedule=cosineScheduler(0.1,0.001,20,2);
const lrs=Array.from({length:80},()=>schedule());

[0,10,19,20,25,39,40,50,79].forEach(i=>console.log(`epoch ${i}: lr=${lrs[i]}`));

console.log('\nLR schedule (normalized):');
const norm=lrs.map(v=>(v-.001)/(.1-.001));
const bar=v=>'█'.repeat(Math.round(v*20)).padEnd(20,'░');
[0,10,20,30,40,60,79].forEach(i=>console.log(`e${String(i).padStart(2)} ${bar(norm[i])} ${lrs[i]}`));
JS,
            ],
            [
                'title' => 'L1 and L2 Regularization Comparison', 'difficulty' => 'hard',
                'description' => 'Train two logistic regression models — one with L1 (lasso) and one with L2 (ridge) regularization. Show that L1 produces sparse weights while L2 shrinks weights toward zero.',
                'solution_code' => <<<'JS'
function trainReg(X, y, type='l2', lambda=0.1, lr=0.05, epochs=500) {
    const sig=z=>1/(1+Math.exp(-z));
    let w=new Array(X[0].length).fill(0.5), b=0;
    for(let e=0;e<epochs;e++){
        const dw=new Array(w.length).fill(0);let db=0;
        X.forEach((x,j)=>{const err=sig(x.reduce((s,xi,i)=>s+xi*w[i],b))-y[j];x.forEach((xi,i)=>dw[i]+=err*xi);db+=err;});
        if(type==='l2') w=w.map((wi,i)=>wi-lr*(dw[i]/X.length+lambda*wi));
        else            w=w.map((wi,i)=>wi-lr*(dw[i]/X.length+lambda*Math.sign(wi)));
        b-=lr*db/X.length;
    }
    return{w:w.map(v=>+v.toFixed(4)),b:+b.toFixed(4),sparsity:w.filter(v=>Math.abs(v)<0.01).length/w.length};
}

const X=Array.from({length:50},()=>Array.from({length:8},()=>Math.random()*2-1));
const y=X.map(x=>x[0]+x[1]>0?1:0);
const l1=trainReg(X,y,'l1',0.1);
const l2=trainReg(X,y,'l2',0.1);
console.log('L1 weights:',l1.w,'sparsity:',l1.sparsity);
console.log('L2 weights:',l2.w,'sparsity:',l2.sparsity);
JS,
            ],
            [
                'title' => 'Text Preprocessing Pipeline', 'difficulty' => 'hard',
                'description' => 'Build a reusable NLP preprocessing pipeline: lowercase, remove punctuation, tokenize, remove stop words, and apply stemming (Porter-like suffix stripping).',
                'solution_code' => <<<'JS'
const STOPWORDS=new Set(['a','an','the','and','or','but','in','on','at','to','for','of','with','is','was','are','were','be','been','i','we','you','he','she','it','they','this','that','have','had','has','do','did','will','would','could','should']);

function porterStem(word) {
    return word
        .replace(/ational$/,'ate').replace(/tional$/,'tion')
        .replace(/enci$/,'ence').replace(/anci$/,'ance')
        .replace(/izer$/,'ize').replace(/ising$/,'ise')
        .replace(/izing$/,'ize')
        .replace(/ation$/,'ate')
        .replace(/ness$/,'').replace(/ment$/,'')
        .replace(/fulness$/,'ful').replace(/ousness$/,'ous')
        .replace(/iveness$/,'ive').replace(/ization$/,'ize')
        .replace(/ing$/,w=>w.length>3?'':'ing')
        .replace(/ly$/,'').replace(/ed$/,'');
}

function preprocess(text, options={stem:true,stopwords:true}) {
    const tokens=text.toLowerCase().replace(/[^a-z0-9\s]/g,' ').split(/\s+/).filter(Boolean);
    const filtered=options.stopwords?tokens.filter(t=>!STOPWORDS.has(t)):tokens;
    return options.stem?filtered.map(porterStem):filtered;
}

const texts=['The quick brown foxes are running quickly!','Machine learning models are trained on datasets.'];
texts.forEach(t=>console.log('Input:',t,'\nOutput:',preprocess(t),'\n'));
JS,
            ],
            [
                'title' => 'Tokenizer and N-Gram Generator', 'difficulty' => 'hard',
                'description' => 'Build a tokenizer that maps words to integer IDs and back, plus an n-gram generator that creates sliding windows of n consecutive tokens for language modeling.',
                'solution_code' => <<<'JS'
class Tokenizer {
    constructor(texts, maxVocab=500) {
        const freq={};
        texts.flatMap(t=>t.toLowerCase().match(/\w+/g)||[]).forEach(w=>freq[w]=(freq[w]||0)+1);
        const vocab=['<PAD>','<UNK>',...Object.entries(freq).sort((a,b)=>b[1]-a[1]).slice(0,maxVocab-2).map(e=>e[0])];
        this.w2i=Object.fromEntries(vocab.map((w,i)=>[w,i]));
        this.i2w=vocab;
    }
    encode(text){return(text.toLowerCase().match(/\w+/g)||[]).map(w=>this.w2i[w]??1);}
    decode(ids){return ids.map(i=>this.i2w[i]??'<UNK>').join(' ');}
}

function ngrams(tokens, n=2) {
    return Array.from({length:tokens.length-n+1},(_,i)=>tokens.slice(i,i+n));
}

function ngramLanguageModel(corpus, n=2) {
    const grams=corpus.flatMap(text=>ngrams(text,n));
    const model={};
    grams.forEach(g=>{const ctx=g.slice(0,-1).join('|'),next=g[g.length-1];if(!model[ctx])model[ctx]={};model[ctx][next]=(model[ctx][next]||0)+1;});
    return ctx=>model[ctx]||{};
}

const corpus=['the cat sat on the mat','the cat ate the rat','the rat ran on the mat'];
const tok=new Tokenizer(corpus);
console.log('Vocabulary size:',tok.i2w.length);
const encoded=tok.encode('the cat sat');
console.log('Encoded:',encoded,'Decoded:',tok.decode(encoded));
const lm=ngramLanguageModel(corpus.map(t=>(t.match(/\w+/g)||[])),2);
console.log('After "the":',lm('the'));
JS,
            ],
            [
                'title' => 'TF-IDF Vectorizer', 'difficulty' => 'hard',
                'description' => 'Build a TF-IDF vectorizer from scratch: compute term frequency and inverse document frequency for each word, then represent each document as a sparse TF-IDF vector.',
                'solution_code' => <<<'JS'
function tfidf(docs) {
    const tok=d=>d.toLowerCase().match(/\w+/g)||[];
    const N=docs.length;
    const vocab=new Set(docs.flatMap(tok));
    const df={};
    docs.forEach(d=>new Set(tok(d)).forEach(w=>df[w]=(df[w]||0)+1));
    const idf=Object.fromEntries([...vocab].map(w=>[w,Math.log((N+1)/(df[w]+1))+1]));
    const vectors=docs.map(d=>{
        const tokens=tok(d),n=tokens.length;
        const tf={};tokens.forEach(w=>tf[w]=(tf[w]||0)+1);
        const vec=Object.fromEntries([...vocab].map(w=>[w,((tf[w]||0)/n)*idf[w]]));
        const norm=Math.sqrt(Object.values(vec).reduce((s,v)=>s+v*v,0));
        return Object.fromEntries(Object.entries(vec).map(([w,v])=>[w,+(v/norm).toFixed(4)]));
    });
    const cosine=(a,b)=>{const k=Object.keys(a);return k.reduce((s,w)=>s+(a[w]||0)*(b[w]||0),0);};
    return{vectors,idf,cosine,topTerms:(i,k=5)=>Object.entries(vectors[i]).sort((a,b)=>b[1]-a[1]).slice(0,k)};
}

const docs=['machine learning is fun','deep learning with neural networks','gradient descent in machine learning'];
const{vectors,topTerms,cosine}=tfidf(docs);
docs.forEach((_,i)=>console.log(`Doc ${i} top terms:`,topTerms(i,3)));
console.log('Doc 0 vs 2 similarity:',cosine(vectors[0],vectors[2]).toFixed(4));
JS,
            ],
            [
                'title' => 'Sentiment Lexicon Scorer', 'difficulty' => 'hard',
                'description' => 'Score text sentiment using a weighted lexicon of positive/negative words. Handles negation (not, never, no) by flipping the sentiment of the following word.',
                'solution_code' => <<<'JS'
const LEXICON={
    great:2,excellent:2,fantastic:3,wonderful:2,amazing:3,love:2,happy:2,joy:2,perfect:2,best:2,
    good:1,nice:1,ok:0.5,fine:0.5,like:1,enjoy:1,pleased:1,
    bad:-2,terrible:-3,awful:-3,horrible:-3,worst:-3,hate:-2,sad:-2,angry:-2,fail:-2,
    poor:-1,slow:-1,boring:-1,ugly:-1,dirty:-1,wrong:-1,
};
const NEGATORS=new Set(['not','no','never','neither','nor','barely','hardly','rarely']);

function sentimentScore(text) {
    const tokens=text.toLowerCase().match(/\w+/g)||[];
    let score=0, words=[];
    tokens.forEach((w,i)=>{
        if(LEXICON[w]!==undefined){
            const negated=i>0&&NEGATORS.has(tokens[i-1]);
            const s=negated?-LEXICON[w]:LEXICON[w];
            score+=s; words.push({word:w,score:s,negated});
        }
    });
    return{score:+score.toFixed(2),label:score>0.5?'positive':score<-0.5?'negative':'neutral',words};
}

['The product is absolutely amazing and great!','This is not bad at all, I love it.','Terrible service, the worst experience ever.','The delivery was ok.'].forEach(t=>console.log(sentimentScore(t).label.padEnd(9),t));
JS,
            ],
            [
                'title' => 'Naive Bayes Text Classifier (Multi-class)', 'difficulty' => 'hard',
                'description' => 'Train a multinomial Naive Bayes classifier for multi-class text classification. Supports any number of categories. Uses log probabilities to avoid numerical underflow.',
                'solution_code' => <<<'JS'
function trainNB(labeledDocs) {
    const tok=t=>(t.toLowerCase().match(/\w+/g)||[]);
    const classes=[...new Set(labeledDocs.map(d=>d.label))];
    const n=labeledDocs.length;
    const classData=Object.fromEntries(classes.map(c=>{const docs=labeledDocs.filter(d=>d.label===c);const words=docs.flatMap(d=>tok(d.text));const freq={};words.forEach(w=>freq[w]=(freq[w]||0)+1);return[c,{freq,n:words.length,prior:docs.length/n}];}));
    const vocab=new Set(labeledDocs.flatMap(d=>tok(d.text))).size;
    const predict=text=>{
        const words=tok(text);
        const scores=Object.fromEntries(classes.map(c=>{
            const{freq,n,prior}=classData[c];
            const score=Math.log(prior)+words.reduce((s,w)=>s+Math.log(((freq[w]||0)+1)/(n+vocab)),0);
            return[c,score];
        }));
        const label=Object.entries(scores).sort((a,b)=>b[1]-a[1])[0][0];
        return{label,scores:Object.fromEntries(Object.entries(scores).map(([k,v])=>[k,+v.toFixed(3)]))};
    };
    return predict;
}

const train=[
    {text:'buy cheap pills discount offer',label:'spam'},
    {text:'win free money click here',label:'spam'},
    {text:'meeting tomorrow at 3pm project update',label:'work'},
    {text:'project deadline review code pull request',label:'work'},
    {text:'dinner tonight movie plans weekend',label:'personal'},
    {text:'birthday party family gathering',label:'personal'},
];
const clf=trainNB(train);
['click here for free prize','code review for new feature','dinner and movie plans'].forEach(t=>console.log(`"${t}" → ${clf(t).label}`));
JS,
            ],
            [
                'title' => 'Levenshtein Edit Distance', 'difficulty' => 'hard',
                'description' => 'Compute the minimum number of single-character edits (insert, delete, substitute) to transform one string into another using dynamic programming.',
                'solution_code' => <<<'JS'
function levenshtein(a, b) {
    const m=a.length, n=b.length;
    const dp=Array.from({length:m+1},(_,i)=>Array.from({length:n+1},(_,j)=>j===0?i:0));
    dp[0]=Array.from({length:n+1},(_,j)=>j);
    for(let i=1;i<=m;i++) for(let j=1;j<=n;j++){
        dp[i][j]=a[i-1]===b[j-1]?dp[i-1][j-1]:1+Math.min(dp[i-1][j],dp[i][j-1],dp[i-1][j-1]);
    }
    return dp[m][n];
}

function spellSuggest(word, dictionary, topK=3) {
    return dictionary.map(w=>({word:w,dist:levenshtein(word.toLowerCase(),w.toLowerCase())})).sort((a,b)=>a.dist-b.dist).slice(0,topK);
}

const dict=['hello','world','machine','learning','neural','network','python','javascript','algorithm','cluster'];
[['kitten','sitting'],['machine','machin'],['neural','nerual']].forEach(([a,b])=>console.log(`"${a}"→"${b}": ${levenshtein(a,b)}`));
console.log('\nSpell suggestions for "algorythm":',spellSuggest('algorythm',dict));
JS,
            ],
            [
                'title' => 'Extractive Text Summarizer', 'difficulty' => 'hard',
                'description' => 'Rank sentences by TF-IDF importance and extract the top-k sentences to form a summary. This is an extractive (not generative) summarization approach using graph centrality.',
                'solution_code' => <<<'JS'
function summarize(text, k=2) {
    const sentences=text.match(/[^.!?]+[.!?]+/g)||[text];
    const tok=s=>s.toLowerCase().match(/\w+/g)||[];
    const stop=new Set(['a','an','the','is','was','are','in','on','at','to','and','or','of']);
    const tokenized=sentences.map(s=>tok(s).filter(w=>!stop.has(w)));
    const tf=tokenized.map(tokens=>{const f={};tokens.forEach(w=>f[w]=(f[w]||0)+1);const n=tokens.length||1;return Object.fromEntries(Object.entries(f).map(([w,c])=>[w,c/n]));});
    const allWords=new Set(tokenized.flat());
    const N=sentences.length;
    const idf=Object.fromEntries([...allWords].map(w=>{const df=tokenized.filter(t=>t.includes(w)).length;return[w,Math.log((N+1)/(df+1))+1];}));
    const score=s=>Object.entries(tf[s]).reduce((sum,[w,f])=>sum+f*(idf[w]||0),0);
    const scores=sentences.map((_,i)=>({i,score:score(i)}));
    const top=scores.sort((a,b)=>b.score-a.score).slice(0,k).sort((a,b)=>a.i-b.i);
    return{summary:top.map(t=>sentences[t.i].trim()).join(' '),scores:scores.slice(0,sentences.length)};
}

const text='Machine learning is a subset of artificial intelligence. It enables computers to learn from data without explicit programming. Deep learning uses neural networks with many layers. These networks can recognize patterns in images, text, and audio. Transformers have revolutionized natural language processing.';
const{summary}=summarize(text,2);
console.log('Summary:', summary);
JS,
            ],
            [
                'title' => 'Bigram Language Model with Sampling', 'difficulty' => 'hard',
                'description' => 'Train a bigram language model on a word corpus. Use MLE to estimate P(w_t | w_{t-1}) with Laplace smoothing, then generate text by sampling from the conditional distribution.',
                'solution_code' => <<<'JS'
function bigramLM(corpus, alpha=0.1) {
    const tokens=['<START>',...(corpus.toLowerCase().match(/\w+/g)||[]),'<END>'];
    const vocab=[...new Set(tokens)];
    const V=vocab.length;
    const idx=Object.fromEntries(vocab.map((w,i)=>[w,i]));
    const counts=Array.from({length:V},()=>new Array(V).fill(alpha));
    for(let i=0;i<tokens.length-1;i++) counts[idx[tokens[i]]][idx[tokens[i+1]]]++;
    const probs=counts.map(row=>{const s=row.reduce((a,b)=>a+b,0);return row.map(c=>c/s);});
    function nextWord(word){
        const r=Math.random();let cum=0;
        for(let i=0;i<V;i++){cum+=probs[idx[word]||0][i];if(r<cum)return vocab[i];}
        return '<END>';
    }
    function generate(maxLen=15){
        const out=[];let w='<START>';
        while(out.length<maxLen){w=nextWord(w);if(w==='<END>')break;out.push(w);}
        return out.join(' ');
    }
    return{generate,vocab,probs};
}

const corpus='the cat sat on the mat the cat ate the rat the rat ran under the mat';
const lm=bigramLM(corpus);
console.log('Generated text 1:',lm.generate());
console.log('Generated text 2:',lm.generate());
JS,
            ],
            [
                'title' => 'Rule-Based Pattern-Matching Chatbot', 'difficulty' => 'hard',
                'description' => 'Build a rule-based chatbot using regex pattern matching and dynamic response templates. Supports memory (context), greeting detection, and intent classification.',
                'solution_code' => <<<'JS'
function createChatbot(rules) {
    const memory={};
    return function respond(input) {
        const text=input.toLowerCase().trim();
        for(const{pattern,response,extract}of rules){
            const match=text.match(new RegExp(pattern,'i'));
            if(match){
                if(extract) Object.assign(memory,Object.fromEntries(extract.map((k,i)=>[k,match[i+1]])));
                const reply=typeof response==='function'?response(match,memory):response;
                return reply.replace(/\{(\w+)\}/g,(_,k)=>memory[k]||k);
            }
        }
        return "I'm not sure I understand. Can you rephrase that?";
    };
}

const bot=createChatbot([
    {pattern:'my name is (\\w+)',extract:['name'],response:'Nice to meet you, {name}!'},
    {pattern:'what is my name',response:'Your name is {name}.'},
    {pattern:'hello|hi|hey',response:'Hello! How can I help you today?'},
    {pattern:'what is (\\d+) \\+ (\\d+)',response:(m)=>`${m[1]} + ${m[2]} = ${+m[1]+ +m[2]}`},
    {pattern:'bye|goodbye',response:'Goodbye! Have a great day!'},
    {pattern:'how are you',response:"I'm doing great, thanks for asking!"},
]);

['Hello!','My name is Alice','What is my name?','What is 5 + 7?','Goodbye!'].forEach(msg=>console.log(`User: ${msg}\nBot: ${bot(msg)}\n`));
JS,
            ],
            [
                'title' => 'Language Identifier Using N-Grams', 'difficulty' => 'hard',
                'description' => 'Detect the language of a text using character trigram frequency profiles. Build a trigram profile for each language and classify new text by comparing trigram distributions.',
                'solution_code' => <<<'JS'
function buildProfile(text, n=3) {
    const s=text.toLowerCase().replace(/[^a-z ]/g,'');
    const freq={};
    for(let i=0;i<=s.length-n;i++){const g=s.slice(i,i+n);freq[g]=(freq[g]||0)+1;}
    const total=Object.values(freq).reduce((a,b)=>a+b,0);
    return Object.fromEntries(Object.entries(freq).map(([k,v])=>[k,v/total]));
}

function detectLanguage(text, profiles) {
    const testProfile=buildProfile(text);
    const scores=Object.entries(profiles).map(([lang,profile])=>{
        const common=Object.keys(testProfile).filter(g=>profile[g]);
        const sim=common.reduce((s,g)=>s+testProfile[g]*profile[g],0);
        return{lang,similarity:+sim.toFixed(6)};
    });
    return scores.sort((a,b)=>b.similarity-a.similarity)[0];
}

const profiles={
    english:buildProfile('the quick brown fox jumps over the lazy dog and the cat sat on the mat in the sun'),
    spanish:buildProfile('el gato rapido salta sobre el perro perezoso en el sol y la luna brilla'),
    french: buildProfile('le chat rapide saute sur le chien paresseux dans le soleil et la lune brille'),
};

['the cat is running fast','el perro es muy rapido','le chat mange le poisson'].forEach(text=>console.log(`"${text.slice(0,25)}"→`,detectLanguage(text,profiles).lang));
JS,
            ],
            [
                'title' => 'LLM Chat Completion API Pattern', 'difficulty' => 'hard',
                'description' => 'Implement the standard pattern for calling an LLM chat completion API. Includes building the messages array with system/user/assistant roles, handling streaming, and managing token limits.',
                'solution_code' => <<<'JS'
async function fakeChatCompletion({model, messages, maxTokens=256}) {
    await new Promise(r=>setTimeout(r,50));
    const lastUser=messages.filter(m=>m.role==='user').pop()?.content||'';
    const response=`[${model}] Echo: ${lastUser.slice(0,80)} (tokens: ${Math.ceil(lastUser.length/4)})`;
    return{choices:[{message:{role:'assistant',content:response}}],usage:{prompt_tokens:Math.ceil(messages.reduce((s,m)=>s+m.content.length,0)/4),completion_tokens:Math.ceil(response.length/4)}};
}

class LLMClient {
    constructor(model='claude-sonnet-4-6'){this.model=model;this.history=[];}
    addSystem(prompt){this.history=[{role:'system',content:prompt},...this.history.filter(m=>m.role!=='system')];}
    async chat(userMessage){
        this.history.push({role:'user',content:userMessage});
        const res=await fakeChatCompletion({model:this.model,messages:this.history,maxTokens:256});
        const reply=res.choices[0].message.content;
        this.history.push({role:'assistant',content:reply});
        return{reply,usage:res.usage,turns:this.history.filter(m=>m.role==='user').length};
    }
}

(async()=>{
    const client=new LLMClient();
    client.addSystem('You are a helpful AI assistant.');
    const r1=await client.chat('What is machine learning?');
    console.log('Turn 1:',r1.reply.slice(0,80));
    const r2=await client.chat('Can you give me an example?');
    console.log('Turn 2:',r2.reply.slice(0,80));
    console.log('Total turns:',r2.turns);
})();
JS,
            ],
            [
                'title' => 'In-Memory Vector Store', 'difficulty' => 'hard',
                'description' => 'Build an in-memory vector store that stores document embeddings and supports cosine similarity search. This is the core data structure behind semantic search and RAG systems.',
                'solution_code' => <<<'JS'
class VectorStore {
    constructor(){this.docs=[];}
    cosine(a,b){const dot=a.reduce((s,v,i)=>s+v*b[i],0),na=Math.sqrt(a.reduce((s,v)=>s+v*v,0)),nb=Math.sqrt(b.reduce((s,v)=>s+v*v,0));return na&&nb?dot/(na*nb):0;}
    add(id, text, embedding){this.docs.push({id,text,embedding});}
    query(queryEmbedding, topK=3){return this.docs.map(d=>({...d,score:+this.cosine(d.embedding,queryEmbedding).toFixed(4)})).sort((a,b)=>b.score-a.score).slice(0,topK);}
    delete(id){this.docs=this.docs.filter(d=>d.id!==id);}
    size(){return this.docs.length;}
}

function mockEmbed(text){
    const words=text.toLowerCase().split(' ');
    const features=['machine','learning','neural','network','data','science','python','javascript','ai','deep'];
    return features.map(f=>words.filter(w=>w.includes(f)).length/words.length);
}

const store=new VectorStore();
['machine learning tutorial','deep neural networks explained','data science with python','javascript web development','AI and machine learning fundamentals'].forEach((t,i)=>store.add(i,t,mockEmbed(t)));
const query='learn machine learning';
const results=store.query(mockEmbed(query));
console.log(`Top ${results.length} results for "${query}":`);
results.forEach(r=>console.log(` [${r.score}] ${r.text}`));
JS,
            ],
            [
                'title' => 'TF-IDF Document Embeddings for Semantic Search', 'difficulty' => 'hard',
                'description' => 'Use TF-IDF vectors as document embeddings for semantic similarity search. Vectorize a corpus, then find the most similar documents to a query using cosine similarity.',
                'solution_code' => <<<'JS'
function buildTFIDF(docs) {
    const tok=d=>(d.toLowerCase().match(/\w+/g)||[]).filter(w=>w.length>2);
    const N=docs.length;
    const allTerms=new Set(docs.flatMap(tok));
    const df={};docs.forEach(d=>new Set(tok(d)).forEach(w=>df[w]=(df[w]||0)+1));
    const idf=Object.fromEntries([...allTerms].map(w=>[w,Math.log((N+1)/(df[w]+1))+1]));
    function vectorize(text){
        const tokens=tok(text),n=tokens.length||1;
        const tf={};tokens.forEach(w=>tf[w]=(tf[w]||0)+1);
        const vec=[...allTerms].map(w=>((tf[w]||0)/n)*(idf[w]||0));
        const norm=Math.sqrt(vec.reduce((s,v)=>s+v*v,0))||1;
        return vec.map(v=>v/norm);
    }
    const cosine=(a,b)=>a.reduce((s,v,i)=>s+v*b[i],0);
    const vectors=docs.map(vectorize);
    return{vectorize,cosine,search:(query,k=3)=>{const qv=vectorize(query);return docs.map((d,i)=>({doc:d,score:+cosine(qv,vectors[i]).toFixed(4)})).sort((a,b)=>b.score-a.score).slice(0,k);}};
}

const docs=['Introduction to machine learning algorithms','Deep learning and neural network architectures','Natural language processing with transformers','Computer vision and image recognition','Reinforcement learning and game AI','Data preprocessing and feature engineering'];
const engine=buildTFIDF(docs);
['neural network tutorial','language model','data cleaning steps'].forEach(q=>{console.log(`\nQuery: "${q}"`);engine.search(q,2).forEach(r=>console.log(`  [${r.score}] ${r.doc}`));});
JS,
            ],
            [
                'title' => 'Sentence Text Chunker for RAG', 'difficulty' => 'hard',
                'description' => 'Split a long document into overlapping chunks for RAG ingestion. Each chunk has a target size (in tokens) and an overlap with the previous chunk to preserve context across boundaries.',
                'solution_code' => <<<'JS'
function chunkText(text, chunkSize=100, overlap=20) {
    const tokens=text.match(/\S+/g)||[];
    const chunks=[];let start=0;
    while(start<tokens.length){
        const end=Math.min(start+chunkSize,tokens.length);
        chunks.push({chunkId:chunks.length,text:tokens.slice(start,end).join(' '),start,end,tokenCount:end-start});
        if(end===tokens.length)break;
        start+=chunkSize-overlap;
    }
    return chunks;
}

function chunkBySentence(text, maxSentences=3, overlap=1) {
    const sentences=text.match(/[^.!?]+[.!?]+/g)||[text];
    const chunks=[];let i=0;
    while(i<sentences.length){
        const end=Math.min(i+maxSentences,sentences.length);
        chunks.push({chunkId:chunks.length,sentences:sentences.slice(i,end),text:sentences.slice(i,end).join(' ')});
        i+=maxSentences-overlap;
    }
    return chunks;
}

const text='Machine learning is transforming technology. Neural networks can recognize patterns. Deep learning uses many layers. Transformers power modern NLP. Attention mechanisms are key. Large models need big data. Fine-tuning adapts models. RAG combines retrieval and generation.';
const wordChunks=chunkText(text,20,5);
console.log('Word chunks:', wordChunks.length, 'chunks');
wordChunks.forEach(c=>console.log(`  [${c.start}-${c.end}]: ${c.text.slice(0,50)}...`));
JS,
            ],
            [
                'title' => 'RAG Pipeline (Retrieval-Augmented Generation)', 'difficulty' => 'hard',
                'description' => 'Build a complete RAG pipeline: chunk a knowledge base, build a vector store, retrieve relevant chunks for a query, and format an augmented prompt to send to an LLM.',
                'solution_code' => <<<'JS'
const chunk=(text,n=50)=>{const w=text.split(' ');const chunks=[];for(let i=0;i<w.length;i+=n-10)chunks.push(w.slice(i,i+n).join(' '));return chunks;};
const embed=text=>{const kw=['neural','machine','learning','data','python','deep','training','model'];return kw.map(k=>(text.toLowerCase().match(new RegExp(k,'g'))||[]).length/text.split(' ').length);};
const cosine=(a,b)=>{const d=a.reduce((s,v,i)=>s+v*b[i],0),na=Math.sqrt(a.reduce((s,v)=>s+v*v,0)),nb=Math.sqrt(b.reduce((s,v)=>s+v*v,0));return na&&nb?d/(na*nb):0;};

function buildRAG(documents) {
    const index=documents.flatMap(({id,text})=>chunk(text).map((c,i)=>({docId:id,chunkId:i,text:c,vec:embed(c)})));
    const retrieve=(query,k=3)=>{const qv=embed(query);return index.map(c=>({...c,score:+cosine(qv,c.vec).toFixed(4)})).sort((a,b)=>b.score-a.score).slice(0,k);};
    const augment=(query,k=3)=>{const ctx=retrieve(query,k);const context=ctx.map((c,i)=>`[${i+1}] ${c.text}`).join('\n');return{prompt:`Context:\n${context}\n\nQuestion: ${query}\n\nAnswer based on the context above:`,sources:ctx.map(c=>c.docId)};};
    return{retrieve,augment};
}

const docs=[{id:'ml101',text:'Machine learning is a method of data analysis that automates analytical model building using neural networks and deep learning algorithms for training models.'},{id:'nlp101',text:'Natural language processing uses machine learning models to understand text with deep neural network training on large data corpora.'}];
const rag=buildRAG(docs);
const{prompt,sources}=rag.augment('How does deep learning work?');
console.log('Sources:',sources);
console.log('Augmented prompt:\n',prompt);
JS,
            ],
            [
                'title' => 'Semantic Search Engine', 'difficulty' => 'hard',
                'description' => 'Build a semantic search engine using TF-IDF embeddings. Index a document corpus, then find the most semantically relevant results for free-text queries.',
                'solution_code' => <<<'JS'
class SemanticSearchEngine {
    constructor(docs) {
        const tok=t=>(t.toLowerCase().match(/\w+/g)||[]);
        this.docs=docs;
        const N=docs.length;
        const df={};docs.forEach(d=>new Set(tok(d.text)).forEach(w=>df[w]=(df[w]||0)+1));
        this.vocab=[...new Set(docs.flatMap(d=>tok(d.text)))];
        this.idf=Object.fromEntries(this.vocab.map(w=>[w,Math.log((N+1)/(df[w]+1))+1]));
        this.vectors=docs.map(d=>this._vectorize(tok(d.text)));
    }
    _vectorize(tokens){const n=tokens.length||1;const tf={};tokens.forEach(w=>tf[w]=(tf[w]||0)+1);const vec=this.vocab.map(w=>((tf[w]||0)/n)*(this.idf[w]||0));const norm=Math.sqrt(vec.reduce((s,v)=>s+v*v,0))||1;return vec.map(v=>v/norm);}
    _cosine(a,b){return a.reduce((s,v,i)=>s+v*b[i],0);}
    search(query,k=3){const tok=t=>(t.toLowerCase().match(/\w+/g)||[]);const qv=this._vectorize(tok(query));return this.docs.map((d,i)=>({...d,score:+this._cosine(qv,this.vectors[i]).toFixed(4)})).sort((a,b)=>b.score-a.score).slice(0,k);}
}

const docs=[{id:1,title:'Intro to ML',text:'machine learning algorithms supervised unsupervised deep neural network'},{id:2,title:'Python Guide',text:'python programming data science numpy pandas matplotlib visualization'},{id:3,title:'NLP Basics',text:'natural language processing text classification sentiment analysis transformer bert'},{id:4,title:'Computer Vision',text:'image recognition convolutional neural network deep learning object detection'},{id:5,title:'Statistics',text:'probability distributions hypothesis testing regression statistical inference'}];
const engine=new SemanticSearchEngine(docs);
['neural network classification','text analysis','python data analysis'].forEach(q=>{console.log(`\n"${q}":`);engine.search(q,2).forEach(r=>console.log(`  [${r.score}] ${r.title}`));});
JS,
            ],
            [
                'title' => 'Keyword-Based Document QA', 'difficulty' => 'hard',
                'description' => 'Build a simple document QA system: given a question, retrieve the most relevant sentence from the document using TF-IDF scoring, then return it as the answer.',
                'solution_code' => <<<'JS'
function documentQA(document, questions) {
    const tok=t=>(t.toLowerCase().match(/\w+/g)||[]);
    const stop=new Set(['a','an','the','is','was','are','in','on','at','to','and','or','of','what','how','why','when','where','who','does']);
    const clean=t=>tok(t).filter(w=>!stop.has(w));
    const sentences=document.match(/[^.!?]+[.!?]+/g)||[];
    function score(question, sentence) {
        const qw=new Set(clean(question));
        const sw=clean(sentence);
        const overlap=sw.filter(w=>qw.has(w)).length;
        return overlap/(Math.sqrt(qw.size)*Math.sqrt(sw.length)||1);
    }
    return questions.map(q=>{
        const scored=sentences.map(s=>({sentence:s.trim(),score:score(q,s)}));
        scored.sort((a,b)=>b.score-a.score);
        return{question:q,answer:scored[0].sentence,confidence:+scored[0].score.toFixed(4)};
    });
}

const doc='Machine learning is a type of artificial intelligence. ML algorithms learn patterns from training data. Supervised learning uses labeled examples to train models. Unsupervised learning finds patterns in unlabeled data. Deep learning uses multi-layer neural networks. Transformers are used for natural language processing tasks.';
const questions=['What is machine learning?','How does supervised learning work?','What are deep learning networks?'];
documentQA(doc,questions).forEach(r=>console.log(`Q: ${r.question}\nA: ${r.answer}\n`));
JS,
            ],
            [
                'title' => 'Conversation Memory Manager', 'difficulty' => 'hard',
                'description' => 'Build a conversation memory manager that stores turns, summarizes old context when it exceeds a token budget, and injects a summary into the system prompt for LLM context continuity.',
                'solution_code' => <<<'JS'
class ConversationMemory {
    constructor(maxTurns=10, summaryEvery=5) {
        this.turns=[];this.summary='';this.maxTurns=maxTurns;this.summaryEvery=summaryEvery;
    }
    addTurn(role, content) {
        this.turns.push({role,content,timestamp:this.turns.length});
        if(this.turns.length>this.maxTurns) this._compress();
    }
    _compress() {
        const old=this.turns.splice(0,this.summaryEvery);
        const summary=old.map(t=>`${t.role}: ${t.content.slice(0,50)}`).join('; ');
        this.summary=this.summary?`${this.summary} | ${summary}`:summary;
    }
    buildMessages(systemPrompt) {
        const messages=[];
        if(systemPrompt||this.summary) messages.push({role:'system',content:[systemPrompt,this.summary?`Summary: ${this.summary}`:''].filter(Boolean).join('\n')});
        return[...messages,...this.turns.map(t=>({role:t.role,content:t.content}))];
    }
    stats(){return{turns:this.turns.length,hasSummary:!!this.summary,totalContext:this.turns.length+(this.summary?1:0)};}
}

const mem=new ConversationMemory(6,3);
[['user','Hello, I am learning ML'],['assistant','Great! I can help you learn machine learning.'],['user','What is gradient descent?'],['assistant','Gradient descent is an optimization algorithm.'],['user','How does backpropagation work?'],['assistant','Backpropagation computes gradients layer by layer.'],['user','What activation functions should I use?']].forEach(([r,c])=>mem.addTurn(r,c));
const msgs=mem.buildMessages('You are an ML tutor.');
console.log('Stats:',mem.stats());
msgs.forEach(m=>console.log(`[${m.role}]: ${m.content.slice(0,70)}`));
JS,
            ],
            [
                'title' => 'Multi-Document Search Index', 'difficulty' => 'hard',
                'description' => 'Build a multi-document inverted index for fast keyword search. Supports boolean AND queries, phrase matching, and returns results ranked by TF-IDF with document metadata.',
                'solution_code' => <<<'JS'
class SearchIndex {
    constructor(){this.docs={};this.inverted={};}
    index(id, text, metadata={}) {
        const tokens=(text.toLowerCase().match(/\w+/g)||[]);
        this.docs[id]={text,metadata,tokens};
        tokens.forEach((w,pos)=>{
            if(!this.inverted[w])this.inverted[w]={};
            if(!this.inverted[w][id])this.inverted[w][id]=[];
            this.inverted[w][id].push(pos);
        });
    }
    search(query, k=3) {
        const qTerms=(query.toLowerCase().match(/\w+/g)||[]);
        const N=Object.keys(this.docs).length;
        const scores={};
        qTerms.forEach(term=>{
            if(!this.inverted[term])return;
            const df=Object.keys(this.inverted[term]).length;
            const idf=Math.log((N+1)/(df+1))+1;
            Object.entries(this.inverted[term]).forEach(([id,positions])=>{
                const tf=positions.length/this.docs[id].tokens.length;
                scores[id]=(scores[id]||0)+tf*idf;
            });
        });
        return Object.entries(scores).sort((a,b)=>b[1]-a[1]).slice(0,k).map(([id,score])=>({id,...this.docs[id].metadata,score:+score.toFixed(4),text:this.docs[id].text.slice(0,60)+'...'}));
    }
}

const idx=new SearchIndex();
[{id:'1',t:'machine learning neural networks deep learning',m:{title:'ML Basics'}},{id:'2',t:'natural language processing text classification neural',m:{title:'NLP Guide'}},{id:'3',t:'deep learning computer vision image recognition CNN',m:{title:'CV Deep Learning'}},{id:'4',t:'reinforcement learning agent reward neural policy',m:{title:'RL Tutorial'}}].forEach(d=>idx.index(d.id,d.t,d.m));
['neural learning','deep vision','language model'].forEach(q=>{console.log(`\nSearch: "${q}"`);idx.search(q,2).forEach(r=>console.log(`  [${r.score}] ${r.title}`));});
JS,
            ],
            [
                'title' => 'Hierarchical Retrieval (Two-Stage Pipeline)', 'difficulty' => 'hard',
                'description' => 'Implement a two-stage retrieval pipeline: first retrieve a large candidate set using fast BM25-style keyword scoring, then re-rank with TF-IDF cosine similarity for precision.',
                'solution_code' => <<<'JS'
function bm25Score(query, doc, k1=1.5, b=0.75, avgDocLen=10) {
    const qTerms=query.toLowerCase().match(/\w+/g)||[];
    const docTerms=doc.toLowerCase().match(/\w+/g)||[];
    const docLen=docTerms.length;
    const tf={};docTerms.forEach(w=>tf[w]=(tf[w]||0)+1);
    return qTerms.reduce((s,w)=>{
        const f=tf[w]||0;
        return s+f*(k1+1)/(f+k1*(1-b+b*docLen/avgDocLen));
    },0);
}

function tfidfCosine(query, doc) {
    const tok=t=>(t.toLowerCase().match(/\w+/g)||[]);
    const qw=tok(query),dw=tok(doc);
    const vocab=new Set([...qw,...dw]);
    const tfidf=(words,term)=>{const tf=words.filter(w=>w===term).length/words.length;const idf=Math.log(2/(words.filter(w=>w===term).length?1:0)+1);return tf*idf;};
    const qv=[...vocab].map(t=>qw.filter(w=>w===t).length/qw.length);
    const dv=[...vocab].map(t=>tfidf(dw,t));
    const dot=qv.reduce((s,v,i)=>s+v*dv[i],0);
    const nq=Math.sqrt(qv.reduce((s,v)=>s+v*v,0)),nd=Math.sqrt(dv.reduce((s,v)=>s+v*v,0));
    return nq&&nd?dot/(nq*nd):0;
}

function twoStageSearch(query, docs, k1=20, k2=5) {
    const avgLen=docs.reduce((s,d)=>(d.text.match(/\w+/g)||[]).length+s,0)/docs.length;
    const stage1=docs.map(d=>({...d,bm25:bm25Score(query,d.text,1.5,0.75,avgLen)})).sort((a,b)=>b.bm25-a.bm25).slice(0,k1);
    return stage1.map(d=>({...d,cosine:+tfidfCosine(query,d.text).toFixed(4)})).sort((a,b)=>b.cosine-a.cosine).slice(0,k2);
}

const docs=[{id:1,title:'ML Intro',text:'machine learning is about learning patterns from data using algorithms'},{id:2,title:'Deep Learning',text:'deep learning neural networks use gradient descent to learn representations'},{id:3,title:'NLP Guide',text:'natural language processing text classification using machine learning models'},{id:4,title:'Databases',text:'relational database management systems and sql query optimization'},{id:5,title:'Computer Vision',text:'image recognition deep learning convolutional networks feature detection'},{id:6,title:'Statistics',text:'statistical learning inference probability distributions regression'}];
console.log('Two-stage results for "machine learning patterns":');
twoStageSearch('machine learning patterns',docs).forEach(r=>console.log(`  [bm25=${r.bm25.toFixed(2)}, cos=${r.cosine}] ${r.title}`));
JS,
            ],
        ];
    }
}
