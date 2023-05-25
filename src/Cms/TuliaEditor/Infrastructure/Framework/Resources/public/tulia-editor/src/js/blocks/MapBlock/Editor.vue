<template>
    <div :class="{ 'tued-simplemap': true, 'tued-simplemap-editable': settings.editable }">
        <div class="tued-simplemap-overlay">
            <button type="button" class="tued-btn" @click="editMap">{{ translator.trans('editMap') }}</button>
        </div>
        <div class="tued-simplemap-editable-overlay">
            <button type="button" class="tued-btn" @click="finishEditing">{{ translator.trans('finishMapEditing') }}</button>
        </div>
        <div :id="mapId" :style="{ height: block.config.height + 'px' }"></div>
    </div>
</template>

<style lang="scss" scoped>
    .tued-simplemap {
        position: relative;
        z-index: 1;

        .tued-simplemap-overlay {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,.2);
            transition: .12s all;
            z-index: 999999;
            opacity: 0;

            button {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
        }

        .tued-simplemap-editable-overlay {
            position: absolute;
            z-index: 999999;
            top: 0;
            right: 0;
            padding: 5px;
            display: none;
        }

        &:hover .tued-simplemap-overlay {
            opacity: 1;
        }

        &.tued-simplemap-editable {
            .tued-simplemap-overlay {
                display: none;
            }
            .tued-simplemap-editable-overlay {
                display: block;
            }
        }
    }
</style>

<style lang="scss">
.leaflet-div-icon {
    background-color: transparent;
    border-color: transparent;
}
.tued-map-div-icon {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-100%);
    width: 25px;
    height: 41px;
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAApCAYAAADAk4LOAAAFgUlEQVR4Aa1XA5BjWRTN2oW17d3YaZtr2962HUzbDNpjszW24mRt28p47v7zq/bXZtrp/lWnXr337j3nPCe85NcypgSFdugCpW5YoDAMRaIMqRi6aKq5E3YqDQO3qAwjVWrD8Ncq/RBpykd8oZUb/kaJutow8r1aP9II0WmLKLIsJyv1w/kqw9Ch2MYdB++12Onxee/QMwvf4/Dk/Lfp/i4nxTXtOoQ4pW5Aj7wpici1A9erdAN2OH64x8OSP9j3Ft3b7aWkTg/Fm91siTra0f9on5sQr9INejH6CUUUpavjFNq1B+Oadhxmnfa8RfEmN8VNAsQhPqF55xHkMzz3jSmChWU6f7/XZKNH+9+hBLOHYozuKQPxyMPUKkrX/K0uWnfFaJGS1QPRtZsOPtr3NsW0uyh6NNCOkU3Yz+bXbT3I8G3xE5EXLXtCXbbqwCO9zPQYPRTZ5vIDXD7U+w7rFDEoUUf7ibHIR4y6bLVPXrz8JVZEql13trxwue/uDivd3fkWRbS6/IA2bID4uk0UpF1N8qLlbBlXs4Ee7HLTfV1j54APvODnSfOWBqtKVvjgLKzF5YdEk5ewRkGlK0i33Eofffc7HT56jD7/6U+qH3Cx7SBLNntH5YIPvODnyfIXZYRVDPqgHtLs5ABHD3YzLuespb7t79FY34DjMwrVrcTuwlT55YMPvOBnRrJ4VXTdNnYug5ucHLBjEpt30701A3Ts+HEa73u6dT3FNWwflY86eMHPk+Yu+i6pzUpRrW7SNDg5JHR4KapmM5Wv2E8Tfcb1HoqqHMHU+uWDD7zg54mz5/2BSnizi9T1Dg4QQXLToGNCkb6tb1NU+QAlGr1++eADrzhn/u8Q2YZhQVlZ5+CAOtqfbhmaUCS1ezNFVm2imDbPmPng5wmz+gwh+oHDce0eUtQ6OGDIyR0uUhUsoO3vfDmmgOezH0mZN59x7MBi++WDL1g/eEiU3avlidO671bkLfwbw5XV2P8Pzo0ydy4t2/0eu33xYSOMOD8hTf4CrBtGMSoXfPLchX+J0ruSePw3LZeK0juPJbYzrhkH0io7B3k164hiGvawhOKMLkrQLyVpZg8rHFW7E2uHOL888IBPlNZ1FPzstSJM694fWr6RwpvcJK60+0HCILTBzZLFNdtAzJaohze60T8qBzyh5ZuOg5e7uwQppofEmf2++DYvmySqGBuKaicF1blQjhuHdvCIMvp8whTTfZzI7RldpwtSzL+F1+wkdZ2TBOW2gIF88PBTzD/gpeREAMEbxnJcaJHNHrpzji0gQCS6hdkEeYt9DF/2qPcEC8RM28Hwmr3sdNyht00byAut2k3gufWNtgtOEOFGUwcXWNDbdNbpgBGxEvKkOQsxivJx33iow0Vw5S6SVTrpVq11ysA2Rp7gTfPfktc6zhtXBBC+adRLshf6sG2RfHPZ5EAc4sVZ83yCN00Fk/4kggu40ZTvIEm5g24qtU4KjBrx/BTTH8ifVASAG7gKrnWxJDcU7x8X6Ecczhm3o6YicvsLXWfh3Ch1W0k8x0nXF+0fFxgt4phz8QvypiwCCFKMqXCnqXExjq10beH+UUA7+nG6mdG/Pu0f3LgFcGrl2s0kNNjpmoJ9o4B29CMO8dMT4Q5ox8uitF6fqsrJOr8qnwNbRzv6hSnG5wP+64C7h9lp30hKNtKdWjtdkbuPA19nJ7Tz3zR/ibgARbhb4AlhavcBebmTHcFl2fvYEnW0ox9xMxKBS8btJ+KiEbq9zA4RthQXDhPa0T9TEe69gWupwc6uBUphquXgf+/FrIjweHQS4/pduMe5ERUMHUd9xv8ZR98CxkS4F2n3EUrUZ10EYNw7BWm9x1GiPssi3GgiGRDKWRYZfXlON+dfNbM+GgIwYdwAAAAASUVORK5CYII=');
}
</style>

<script setup>
import { defineProps, inject, onMounted, computed, reactive, watch } from "vue";
const L = require('leaflet');
const _ = require('lodash');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const translator = inject('translator');

const mapId = `tued-map-instance-${_.uniqueId()}`;

let position = [block.data.position.lat, block.data.position.lng];
const settings = reactive({
    editable: false,
});

const editMap = () => {
    settings.editable = true;
};
const finishEditing = () => {
    settings.editable = false;
};
let map, tiles, marker;

watch(() => block.config.zoom, async (zoom) => {
    map.setZoom(zoom);
});

onMounted(() => {
    map = L.map(mapId).setView(position, block.config.zoom);
    tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        minZoom: 4,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    marker = L.marker(
        position,
        {
            draggable: true,
            icon: L.divIcon({
                html: '<div class="tued-map-div-icon"></div>',
            })
        }
    ).addTo(map);

    map.on('zoom', (e) => {
        block.send('map.zoom.change', map.getZoom());
    });

    map.on('click', function mapClickListen(e) {
        block.data.position.lat = e.latlng.lat;
        block.data.position.lng = e.latlng.lng;

        marker.setLatLng(e.latlng);
    });

    marker.on('dragend', function(e) {
        const latlng = marker.getLatLng();

        block.data.position.lat = latlng.lat;
        block.data.position.lng = latlng.lng;
    });
});
</script>
<script>
export default { name: 'Block.Map.Editor' }
</script>
