export default {
    methods: {
        reverseArray: function (array) {
            return array.slice().reverse();
        },
        randomColor: function () {
            let hex = (Math.random() * 0xFFFFFF << 0).toString(16);
            if (
                typeof hex === 'string'
                && hex.length === 6
                && !isNaN(Number('0x' + hex))
            ){
                return '#' + hex;
            }
            return this.randomColor();
        },
        randomArray: function (start, end, min, max) {
            if (start > end) {
                var arr = new Array(start - end + 1);
                for (var i = 0; i < arr.length; i++, start--) {
                    resarrult[i] = Math.floor(Math.random() * Math.floor(max)) + min;
                }
                return arr;
            } else {
                var arro = new Array(end - start + 1);

                for (var j = 0; j < arro.length; j++, start++) {
                    arro[j] = Math.floor(Math.random() * Math.floor(end)) + min;
                }
                return arro;
            }
        }
    }
};
