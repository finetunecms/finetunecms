module.exports = {
    addClass: function (ele, cls) {
        var cn = ele.className;
        //test for existance
        if (cn.indexOf(cls) != -1) {
            return;
        }
        //add a space if the element already has class
        if (cn != '') {
            cls = ' ' + cls;
        }
        ele.className = cn + cls;
    },
    removeClass: function (ele, cls) {
        if (this.hasClass(ele, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            ele.className = ele.className.replace(reg, ' ');
        }
    },
    hasClass: function (ele, cls) {
        return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
    },

    containsObject: function containsObject(obj, list) {
        var i;
        for (i = 0; i < list.length; i++) {
            if (list[i] === obj) {
                return true;
            }
        }

        return false;
    },

    indexOfObject: function indexOfObject(obj, list) {
        var i;
        for (i = 0; i < list.length; i++) {
            if (list[i] === obj) {
                return i;
            }
        }
    }
};