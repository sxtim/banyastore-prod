export default function productCardImages() {
    if (!window.matchMedia('(min-width: 651px)').matches) {
        return;
    }

    const maxCanvasSize = 180;
    const targetContentWidth = 196;
    const targetContentHeight = 180;
    const maxScale = 1.14;

    const isBackgroundPixel = (data, index) => {
        const alpha = data[index + 3];
        const red = data[index];
        const green = data[index + 1];
        const blue = data[index + 2];

        return alpha <= 10 || (red >= 246 && green >= 246 && blue >= 246);
    };

    const getContentBounds = (image) => {
        const naturalWidth = image.naturalWidth;
        const naturalHeight = image.naturalHeight;

        if (!naturalWidth || !naturalHeight) {
            return null;
        }

        const ratio = Math.min(maxCanvasSize / naturalWidth, maxCanvasSize / naturalHeight, 1);
        const width = Math.max(1, Math.round(naturalWidth * ratio));
        const height = Math.max(1, Math.round(naturalHeight * ratio));
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', {willReadFrequently: true});

        if (!context) {
            return null;
        }

        canvas.width = width;
        canvas.height = height;
        context.drawImage(image, 0, 0, width, height);

        const pixels = context.getImageData(0, 0, width, height).data;
        let minX = width;
        let minY = height;
        let maxX = -1;
        let maxY = -1;

        for (let y = 0; y < height; y += 1) {
            for (let x = 0; x < width; x += 1) {
                const index = (y * width + x) * 4;

                if (isBackgroundPixel(pixels, index)) {
                    continue;
                }

                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x);
                maxY = Math.max(maxY, y);
            }
        }

        if (maxX < 0 || maxY < 0) {
            return null;
        }

        return {
            left: minX / ratio,
            top: minY / ratio,
            width: (maxX - minX + 1) / ratio,
            height: (maxY - minY + 1) / ratio,
        };
    };

    const normalizeImage = (image) => {
        const picture = image.closest('.card__picture');

        if (!picture || !image.naturalWidth || !image.naturalHeight) {
            return;
        }

        let bounds = null;

        try {
            bounds = getContentBounds(image);
        } catch (error) {
            return;
        }

        if (!bounds) {
            return;
        }

        const boxWidth = picture.clientWidth;
        const boxHeight = picture.clientHeight;
        const fittedImageScale = Math.min(boxWidth / image.naturalWidth, boxHeight / image.naturalHeight);
        const fittedImageWidth = image.naturalWidth * fittedImageScale;
        const fittedImageHeight = image.naturalHeight * fittedImageScale;
        const renderedBounds = {
            left: (boxWidth - fittedImageWidth) / 2 + bounds.left * fittedImageScale,
            top: (boxHeight - fittedImageHeight) / 2 + bounds.top * fittedImageScale,
            width: bounds.width * fittedImageScale,
            height: bounds.height * fittedImageScale,
        };
        const scale = Math.min(
            maxScale,
            Math.max(
                1,
                Math.min(targetContentWidth / renderedBounds.width, targetContentHeight / renderedBounds.height)
            )
        );
        const contentCenterX = renderedBounds.left + renderedBounds.width / 2;
        const contentCenterY = renderedBounds.top + renderedBounds.height / 2;
        const offsetX = (boxWidth / 2 - contentCenterX) / scale;
        const offsetY = (boxHeight / 2 - contentCenterY) / scale;

        image.style.setProperty('--card-image-scale', scale.toFixed(3));
        image.style.setProperty('--card-image-offset-x', `${offsetX.toFixed(1)}px`);
        image.style.setProperty('--card-image-offset-y', `${offsetY.toFixed(1)}px`);
        image.classList.add('card__picture-img--normalized');
    };

    document.querySelectorAll('.card__picture img').forEach((image) => {
        if (image.complete) {
            normalizeImage(image);
            return;
        }

        image.addEventListener('load', () => normalizeImage(image), {once: true});
    });
}
