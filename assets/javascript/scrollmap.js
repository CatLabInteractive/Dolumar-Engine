/* JavaScript by Lumpio- (Matti Virkkunen)
 * Update: heavily edited by Thijs Van der Schaeghe.
 ***/

function TiledImageViewer(
    element,
    tilecb,
    tilecb2,
    mapwidth,
    mapheight,
    viewwidth,
    viewheight,
    imagewidth,
    imageheight,
    startX,
    startY,
    onscroll
) {
    var x, y;

    this.loadedMapElements = new Array();
    this.loadedMapTiles = new Object();
    this.countLoadedImages = 0;

    this.drawFunction = tilecb;
    this.commitDraw = tilecb2;
    this.onscroll = onscroll;

    this.el = element;
    this.el._TIV_obj = this;

    this.tileX = imagewidth;
    this.tileY = imageheight;

    this.div = document.createElement('div');

    this.el.appendChild(this.div);

    this.div.style.position = 'absolute';

    this.div.style.left = (startX + viewwidth / 2) + 'px';
    this.div.style.top = (startY + viewheight / 2) + 'px';

    // Start calculations
    this.resize(viewwidth, viewheight);

    this.div.style.width = viewwidth + 'px';
    this.div.style.height = viewheight + 'px';

    // Make pointer
    var pointer = document.createElement('div');
    pointer.style.display = 'none';
    this.el.appendChild(pointer);

    this.el.tivpointer = pointer;

    this.selecting = false;
    this.overlay = null;
}

TiledImageViewer.prototype.setTilesizes = function (imagewidth, imageheight) {
    // Calculate zoom
    var zx = imagewidth / this.tileX;
    var zy = imageheight / this.tileY;

    this.tileX = imagewidth;
    this.tileY = imageheight;

    // Recenter
    var locx = parseInt(this.div.style.left) * zx;
    var locy = parseInt(this.div.style.top) * zy;

    locx += (parseInt(this.div.style.width) / 2) * (1 - zx);
    locy += (parseInt(this.div.style.height) / 2) * (1 - zy);

    this.jumpTo(locx, locy, false);

    /*
    this.div.style.left = locx + 'px';
    this.div.style.top = locy + 'px';
    */

    this.resize();
    this.reloadAllTiles();
    this.drawVisibleImages();
}

TiledImageViewer.prototype.getLocation = function () {
    var obj =
        {
            'left': parseInt(this.div.style.left) * -1,
            'top': parseInt(this.div.style.top) * -1
        };

    return obj;
}

TiledImageViewer.prototype.addInnerHTML = function (ah) {
    this.div.innerHTML += ah;
}

TiledImageViewer.prototype.resize = function (x, y) {
    if (typeof (x) == 'undefined') {
        x = parseInt(this.div.style.width);
    }

    if (typeof (y) == 'undefined') {
        y = parseInt(this.div.style.height);
    }

    this.displaySizeX = Math.ceil((1 * x) / this.tileX) + 1;
    this.displaySizeY = Math.ceil((1 * y) / this.tileY) + 1;

    this.div.style.width = x + 'px';
    this.div.style.height = y + 'px';

    // Check for new images
    this.drawVisibleImages();
}

TiledImageViewer.prototype.drawVisibleImages = function () {
    var newLoadedTiles = 0;

    var x = Math.floor(parseInt(this.div.style.left) / parseInt(this.tileX));
    var y = Math.floor(parseInt(this.div.style.top) / parseInt(this.tileY));

    var newLoadedMapTiles = new Object();

    for (var i = (0 - this.displaySizeX - x + 1); i < (this.displaySizeX - x - 1); i++) {
        if (this.loadedMapElements[i] == undefined) {
            this.loadedMapElements[i] = new Array();
        }

        for (var j = (0 - this.displaySizeY - y + 1); j < (this.displaySizeY - y - 1); j++) {
            // Check for array
            if (this.loadedMapElements[i][j] != 1) {
                this.drawImage(i, j);
                this.loadedMapElements[i][j] = 1;
                this.countLoadedImages++;
                this.newLoadedTiles++;
            }
            newLoadedMapTiles[i + '|' + j] = new Array(i, j);
            delete (this.loadedMapTiles[i + '|' + j]);
        }
    }

    // Commit draw
    this.commitDraw();

    // Remove unused objects
    for (var rem in this.loadedMapTiles) {
        var element = document.getElementById(this.el.id + this.loadedMapTiles[rem][0] + 'p' + this.loadedMapTiles[rem][1]);

        if (element) {
            element.innerHTML = '';
            this.div.removeChild(element);
            delete (this.loadedMapElements[this.loadedMapTiles[rem][0]][this.loadedMapTiles[rem][1]]);
            this.countLoadedImages--;
        }
    }

    // Replace loadedMapTiles
    this.loadedMapTiles = newLoadedMapTiles;

    // Counted image
    if (document.getElementById('imageCounter')) {
        document.getElementById('imageCounter').innerHTML = this.countLoadedImages;
    }

    // Current location
    if (document.getElementById('currentLocation')) {
        document.getElementById('currentLocation').innerHTML = parseInt(this.div.style.left) + ',' + parseInt(this.div.style.top);
    }

    if (document.getElementById('newTiles')) {
        document.getElementById('newTiles').innerHTML = this.newLoadedTiles;
    }
}

TiledImageViewer.prototype.reloadAllTiles = function () {

    // Remove unused objects
    for (var rem in this.loadedMapTiles) {
        var element = document.getElementById(this.el.id + this.loadedMapTiles[rem][0] + 'p' + this.loadedMapTiles[rem][1]);

        if (element) {
            element.innerHTML = '';
            this.div.removeChild(element);
            delete (this.loadedMapElements[this.loadedMapTiles[rem][0]][this.loadedMapTiles[rem][1]]);
            this.countLoadedImages--;
        }
    }

    this.drawVisibleImages();
}

TiledImageViewer.prototype.reloadTiles = function (a) {
    for (var c = 0; c < a.length; c++) {
        var i = a[c][0];
        var j = a[c][1];
        if (this.loadedMapElements[i] && this.loadedMapElements[i][j]) {
            this.drawFunction(i, j);
        }
    }
    this.commitDraw();
}

TiledImageViewer.prototype.drawImage = function (x, y) {

    var div = document.createElement('div');

    div.id = this.el.id + x + 'p' + y;
    div.style.position = 'absolute';
    div.style.top = (y * this.tileY) + 'px';
    div.style.left = (x * this.tileX) + 'px';
    div.style.width = this.tileX + 'px';
    div.style.height = this.tileY + 'px';
    div.style.overflow = 'hidden';
    div.style.zIndex = 0;

    this.div.appendChild(div);

    // Calculate
    this.drawFunction(x, y);
}

TiledImageViewer.prototype.enableControls = function () {
    var self = this;

    var mouseEvents = true;

    self.el.addEventListener("touchstart", function (e) {
        mouseEvents = false;

        //alert ('test');
        //console.log (event);
        self.el.onmousedown = null;
        TIV_touchBegin(e, self);

        if (e.stopPropagation) e.stopPropagation();
        if (e.preventDefault) e.preventDefault();
        e.cancelBubble = true;
        e.returnValue = false;
        return false;

    }, false);

    self.el.addEventListener("touchend", TIV_touchEnd, false);

    self.el.addEventListener("mousedown", function (e) {
        if (mouseEvents) {
            TIV_dragBegin(e, self);
        }
        if (e.stopPropagation) e.stopPropagation();
        if (e.preventDefault) e.preventDefault();
        e.cancelBubble = true;
        e.returnValue = false;
        return false;
    }, false);

    self.el.addEventListener("mouseup", TIV_dragEnd, false);
    //document.body.onmouseup = TIV_dragEnd;
    //this.el.ondblclick = TIV_recenter;
};

TiledImageViewer.prototype.scrollBy = function (x, y) {

    var nX = parseInt(this.div.style.left);
    var nY = parseInt(this.div.style.top);

    nX = nX - parseInt(x);
    nY = nY - parseInt(y);

    this.div.style.left = nX + 'px';
    this.div.style.top = nY + 'px';

    if (typeof(this.onscroll) != 'undefined') {
        this.onscroll(-nX, -nY, x, y);
    }
}

TiledImageViewer.prototype.jumpTo = function (x, y, center) {

    if (typeof (center) == 'undefined') {
        center = true;
    }

    var oX = parseInt(this.div.style.width);
    var oY = parseInt(this.div.style.height);

    var nX = x;
    var nY = y;

    if (center) {
        nX += Math.ceil(oX / 2);
        nY += Math.ceil(oY / 2);
    }

    this.div.style.left = nX + 'px';
    this.div.style.top = nY + 'px';

    // Recalculate
    this.drawVisibleImages();

    if (typeof(this.onscroll) != 'undefined') {
        this.onscroll(-nX, -nY, (nX - oX), (nY - oY));
    }
}

TiledImageViewer.prototype.updatePointerBackgroundImage = function (image) {
    var pointer = this.el.tivpointer;

    pointer.style.zIndex = '1000';
    pointer.style.position = 'absolute';
    pointer.style.backgroundImage = 'url(' + image.src + ')';

    // Now let's do that tricky image size thing
    var newImg = new Image();

    newImg.onload = function () {
        pointer.style.width = newImg.width + 'px';
        pointer.style.height = newImg.height + 'px';
        pointer.style.display = 'block';
    };

    newImg.src = image.src;

    //pointer.style.width = image.width + 'px';
    //pointer.style.height = image.height + 'px';
}

TiledImageViewer.prototype.selectLocation = function (callback, onFinish, image) {
    var self = this;
    this.selecting = true;

    this.el.onmousedown = TIV_selectLocation;
    this.el.onmouseup = function (e) {
        self.el.onmousedown = null;
        if (!e) {
            e = window.event;
        }

        // Prevent the default action (= showing the right-click-menu)
        if (e.preventDefault)
            e.preventDefault();
        else
            e.returnValue = false;
        return false;
    };

    // Remove the right mouse button.
    this.el.oncontextmenu = function () {
        return false;
    }

    this.el.onmousemove = TIV_redrawLocationPoint;

    this.selLocAct = callback;
    this.onFinish = onFinish;

    this.el.style.cursor = 'pointer';

    var pointer = this.el.tivpointer;
    if (pointer && typeof(image) != 'undefined' && image != null) {
        this.updatePointerBackgroundImage(image);
    }

    // Small annoying popup to show that you cna right click to cancel
    var div = document.getElementById('map_cancel_warning');
    if (div) {
        div.style.display = 'block';
    }

    this.drawOverlay();

    /*
        imgCls:
        0 = class
        1 = width
        2 = height
        3 = offsetX,
        4 = offsetY
    */
}

TiledImageViewer.prototype.drawOverlay = function() {

    this.removeOverlay();

    // Introduce a giant ass overlay to block all inner events
    this.overlay = document.createElement('div');

    /*
    this.overlay.style.background = 'pink';
    this.overlay.style.opacity = '0.5';
     */

    this.overlay.style.position = 'absolute';
    this.overlay.style.top = '0px';
    this.overlay.style.left = '0px';
    this.overlay.style.width = '100%';
    this.overlay.style.height = '100%';
    this.overlay.style.zIndex = 1000000;

    this.el.appendChild(this.overlay);
};

TiledImageViewer.prototype.removeOverlay = function() {
    if (typeof(this.overlay) !== 'undefined' && this.overlay) {
        try {
            this.overlay.parentNode.removeChild(this.overlay);
        } catch (e) {
            console.log(e);
        }
        this.overlay = null;
    }
};

TiledImageViewer.prototype.setDblclickLocation = function (act) {
    this.el.ondblclick = TIV_selectLocationDblClick;
    this.doubleClickAction = act;
}

TiledImageViewer.prototype.dblClickAction = function (x, y) {
    x = (x - parseInt(this.div.style.left));
    y = (y - parseInt(this.div.style.top));

    this.doubleClickAction(x, y);
}

TiledImageViewer.prototype.selectLocationClick = function (x, y) {
    function isFunction(functionToCheck) {
        return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
    }

    x = (x - parseInt(this.div.style.left));
    y = (y - parseInt(this.div.style.top));

    if (this.selLocAct && isFunction(this.selLocAct)) {
        this.selLocAct(x, y);
    }

    this.cancelLocationClick(x, y);
    return false;
};

TiledImageViewer.prototype.cancelLocationClick = function (x, y) {
    try {
        this.onFinish();
    }
    catch (e) {
    }

    this.onFinish = '';
    this.selLocAct = '';
    this.selLocWin = '';

    if (this.el.tivpointer) {
        this.el.tivpointer.style.display = 'none';
    }

    window.onmousemove = '';
    this.enableControls();
    this.el.style.cursor = 'default';

    var self = this;

    // Remove the right mouse button.
    // but wait a few miliseconds to make sure the oncontextmenu trigger
    // did already happen for this call.
    setTimeout
    (
        function () {
            self.el.oncontextmenu = null;
        },
        200
    );

    var div = document.getElementById('map_cancel_warning');
    if (div) {
        div.style.display = 'none';
    }

    this.selecting = false;

    this.removeOverlay();
};

function TIV_selectLocation(e) {
    if (!e) {
        e = window.event;
    }

    // Prevent the default action (= showing the right-click-menu)
    if (e.preventDefault) {
        e.preventDefault();
    } else {
        e.returnValue = false;
    }

    var o = this._TIV_obj;
    var mpos = TIV_getMousePos(e);

    var button = "LEFT";

    if (e.which == null) {
        /* IE case */
        button = (e.button < 2) ? "LEFT" :
            ((e.button == 4) ? "MIDDLE" : "RIGHT");
    } else {
        /* All others */
        button = (e.which < 2) ? "LEFT" :
            ((e.which == 2) ? "MIDDLE" : "RIGHT");
    }

    setTimeout(function() {
        if (button == "LEFT") {
            // Only allow the left button to trigger the event.
            o.selectLocationClick(mpos.x, mpos.y);
        }
        else {
            // Cancel this select
            o.cancelLocationClick(mpos.x, mpos.y);
        }
    }, 250)


    return false;
}

function TIV_redrawLocationPoint(e) {
    if (!e) {
        e = window.event;
    }

    var pointer = this.tivpointer;

    if (pointer && pointer.style.position == 'absolute') {
        var mpos = TIV_getMousePos(e);

        pointer.style.left = (mpos.x - (parseInt(pointer.style.width) / 2)) + 'px';
        pointer.style.top = (mpos.y - (parseInt(pointer.style.height) / 2)) + 'px';
    }
}

function TIV_selectLocationDblClick(e) {
    if (!e) {
        e = window.event;
    }

    var o = this._TIV_obj;
    var mpos = TIV_getMousePos(e);

    var offset = Element.cumulativeOffset(o.el);

    var dx = offset.left;
    var dy = offset.top;

    o.dblClickAction(mpos.x - dx, mpos.y - dy);
}

function TIV_getMousePos(e) {
    return {x: e.clientX, y: e.clientY};
}

function TIV_getTouchPos(e) {
    var out =
        {
            'x': e.targetTouches[0].clientX,
            'y': e.targetTouches[0].clientY
        };

    //console.log (out);
    return out;
}

function TIV_touchBegin(e, self) {
    var o = self.el._TIV_obj;
    var mpos;

    if (!e) {
        e = window.event;
    }

    o.touchStart = new Date();

    mpos = TIV_getTouchPos(e);
    o.mx = o.drmx = mpos.x;
    o.my = o.drmy = mpos.y;

    o.dragging = true;
    self.el.addEventListener('touchmove', TIV_touchMove, false);

    setTimeout(function () {
        TIV_dragUpdate(o);
    }, 50);
}

function TIV_touchEnd(e) {
    var o = this._TIV_obj;

    o.removeOverlay();

    o.dragging = false;
    this.ontouchmove = '';

    // Recalculate
    o.drawVisibleImages();

    return false;
}

function TIV_dragBegin(e, obj) {
    var self = obj;

    var o = self.el._TIV_obj;
    var mpos;

    if (!e) {
        e = window.event;
    }

    mpos = TIV_getMousePos(e);
    o.dragStart = new Date();
    o.mx = o.drmx = mpos.x;
    o.my = o.drmy = mpos.y;
    o.dragging = true;
    self.el.onmousemove = TIV_dragMouseMove;
    self.el.style.cursor = 'move';
    setTimeout(function () {
        TIV_dragUpdate(o);
    }, 50);
    return false;
}

function TIV_touchMove(e) {
    var o = this._TIV_obj;
    var mpos;

    if (!e) {
        e = window.event;
    }

    if (
        !o.overlay &&
        o.touchStart &&
        ((new Date()).getTime() - o.touchStart.getTime()) > 50
    ) {
        o.drawOverlay();
    }

    mpos = TIV_getTouchPos(e);
    o.mx = mpos.x;
    o.my = mpos.y;

    return false;
}

function TIV_dragMouseMove(e) {
    var o = this._TIV_obj;
    var mpos;

    if (!e) {
        e = window.event;
    }

    if (
        !o.overlay &&
        o.dragStart &&
        ((new Date()).getTime() - o.dragStart.getTime()) > 50
    ) {
        o.drawOverlay();
    }

    mpos = TIV_getMousePos(e);
    o.mx = mpos.x;
    o.my = mpos.y;

    return false;
}

function TIV_dragEnd() {
    var o = this._TIV_obj;

    o.dragging = false;
    this.onmousemove = '';
    this.style.cursor = '';

    // Recalculate
    o.drawVisibleImages();
    o.removeOverlay();

    return false;
}

function TIV_dragUpdate(o) {
    if (o.mx != o.drmx || o.my != o.drmy) {
        o.scrollBy(o.drmx - o.mx, o.drmy - o.my);
        o.drmx = o.mx;
        o.drmy = o.my;
    }

    if (o.dragging) {
        setTimeout(function () {
            TIV_dragUpdate(o);
        }, 50);
    }
}

function TIV_recenter(e) {
    var o = this._TIV_obj;
    var mpos, cx, cy, dx, dy;

    if (!e) {
        e = window.event;
    }

    mpos = TIV_getMousePos(e);
    cx = this.offsetLeft + this.offsetWidth / 2;
    cy = this.offsetTop + this.offsetHeight / 2;
    dx = mpos.x - this.offsetLeft - cx;
    dy = mpos.y - this.offsetTop - cy;
    o.scrollBy(dx, dy);
}
