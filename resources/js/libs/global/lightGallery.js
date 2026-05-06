import lightGallery from 'lightgallery'
import lgZoom from 'lightgallery/plugins/zoom'
import lgThumbnail from 'lightgallery/plugins/thumbnail'

export function initLightGallery(element) {
    if (!element) return;

    return lightGallery(element, {
        plugins: [lgZoom, lgThumbnail],
        speed: 300
    });
}
