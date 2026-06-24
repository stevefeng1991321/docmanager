import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.js?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorkerUrl;

window.pdfViewer = function (streamUrl) {
    return {
        currentPage: 1,
        totalPages: 0,
        scale: 1.25,
        loading: true,
        error: '',
        _pdf: null,
        _rendering: false,

        async init() {
            try {
                this._pdf      = await pdfjsLib.getDocument(streamUrl).promise;
                this.totalPages = this._pdf.numPages;
                await this._render(1);
            } catch (e) {
                this.error = 'Could not load PDF. ' + (e.message ?? '');
            } finally {
                this.loading = false;
            }
        },

        async _render(num) {
            if (this._rendering) return;
            this._rendering = true;
            try {
                const page     = await this._pdf.getPage(num);
                const viewport = page.getViewport({ scale: this.scale });
                const canvas   = this.$refs.canvas;
                const ctx      = canvas.getContext('2d');
                canvas.height  = viewport.height;
                canvas.width   = viewport.width;
                await page.render({ canvasContext: ctx, viewport }).promise;
                this.currentPage = num;
            } finally {
                this._rendering = false;
            }
        },

        async prev() { if (this.currentPage > 1)            await this._render(this.currentPage - 1); },
        async next() { if (this.currentPage < this.totalPages) await this._render(this.currentPage + 1); },

        async zoomIn()  { this.scale = Math.min(3.0, +(this.scale + 0.25).toFixed(2)); await this._render(this.currentPage); },
        async zoomOut() { this.scale = Math.max(0.5, +(this.scale - 0.25).toFixed(2)); await this._render(this.currentPage); },

        scalePercent() { return Math.round(this.scale * 100) + '%'; },
    };
};
