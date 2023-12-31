(function ($,window,document,undefined) {
    function SliderUnlock(elm, options, success){
        var me = this;
        var $elm = me.checkElm(elm) ? $(elm) : $;
        success = me.checkFn(success) ? success : function(){};

        var opts = {
			initLabelTip : "Drag the slider submitted",
            successLabelTip:  "Successfully Verified",
            duration:  200,
            swipestart:  false,
            min:  0,
            max:  $elm.width(),
            index:  0,
            IsOk:  false,
            lableIndex: 0
        };

        opts = $.extend(opts, options||{});

        //$elm
        me.elm = $elm;
        //opts
        me.opts = opts;
        //Whether to start the slide
        me.swipestart = opts.swipestart;
        //Minimum
        me.min = opts.min;
        //Maximum
        me.max = opts.max;
        //currentIn which the sliderofposition
        me.index = opts.index;
        //Whether sliding success
        me.isOk = opts.isOk;
        //Width slider
        me.labelWidth = me.elm.find('#label').width();
        //Slider background
        me.sliderBg = me.elm.find('#slider_bg');
        //mouseinslipmovePush buttonofposition
        me.lableIndex = opts.lableIndex;
        //success
        me.success = success;
    }

    SliderUnlock.prototype.init = function () {
        var me = this;
		me.elm.find("#labelTip").text(me.opts.initLabelTip);
        me.updateView();
        me.elm.find("#label").on("mousedown", function (event) {
            var e = event || window.event;
            me.lableIndex = e.clientX - this.offsetLeft;
            me.handerIn();
        }).on("mousemove", function (event) {
            me.handerMove(event);
        }).on("mouseup", function (event) {
            me.handerOut();
        }).on("mouseout", function (event) {
            me.handerOut();
        }).on("touchstart", function (event) {
            var e = event || window.event;
            me.lableIndex = e.originalEvent.touches[0].pageX - this.offsetLeft;
            me.handerIn();
        }).on("touchmove", function (event) {
            me.handerMove(event, "mobile");
        }).on("touchend", function (event) {
            me.handerOut();
        });
    };

    /**
     * mouse/Slide finger contact button
     */
    SliderUnlock.prototype.handerIn = function () {
        var me = this;
        me.swipestart = true;
        me.min = 0;
        me.max = me.elm.width();
    };

    /**
     * mouse/Fingers removed
     */
    SliderUnlock.prototype.handerOut = function () {
        var me = this;
        //stop
        me.swipestart = false;
        //me.move();
        if (me.index < me.max) {
            me.reset();
        }
    };

    /**
     * mouse/手指shiftmove
     * @param event
     * @param type
     */
    SliderUnlock.prototype.handerMove = function (event, type) {
        var me = this;
        if (me.swipestart) {
            event.preventDefault();
            event = event || window.event;
            if (type == "mobile") {
                me.index = event.originalEvent.touches[0].pageX - me.lableIndex;
            } else {
                me.index = event.clientX - me.lableIndex;
            }
            me.move();
        }
    };

    /**
     * mouse/手指shiftmoveLive程
     */
    SliderUnlock.prototype.move = function () {
        var me = this;
        if ((me.index + me.labelWidth) >= me.max) {
            me.index = me.max - me.labelWidth -2;
            //stop
            me.swipestart = false;
            //Unlock
            me.isOk = true;
        }
        if (me.index < 0) {
            me.index = me.min;
            //notUnlock
            me.isOk = false;
        }
        if (me.index+me.labelWidth+2 == me.max && me.max > 0 && me.isOk) {
            //Unlock the default action
            $('#label').unbind().next('#labelTip').
            text(me.opts.successLabelTip).css({'color': '#fff'});

            me.success();
        }
        me.updateView();
    };


    /**
     * Update View
     */
    SliderUnlock.prototype.updateView = function () {
        var me = this;

        me.sliderBg.css('width', me.index);
        me.elm.find("#label").css("left", me.index + "px");

    };

    /**
     * ResetslideThe starting point
     */
    SliderUnlock.prototype.reset = function () {
        var me = this;

        me.index = 0;
        me.sliderBg .animate({'width':0},me.opts.duration);
        me.elm.find("#label").animate({left: me.index}, me.opts.duration)
            .next("#lableTip").animate({opacity: 1}, me.opts.duration);
        me.updateView();
    };

    /**
     * Detecting element exists
     * @param elm
     * @returns {boolean}
     */
    SliderUnlock.prototype.checkElm = function (elm) {
        if($(elm).length > 0){
            return true;
        }else{
            throw "this element does not exist.";
        }
    };

    /**
     * Detect whether the incoming parametersfunction
     * @param fn
     * @returns {boolean}
     */
    SliderUnlock.prototype.checkFn = function (fn) {
        if(typeof fn === "function"){
            return true;
        }else{
            throw "the param is not a function.";
        }
    };

    window['SliderUnlock'] = SliderUnlock;
})(jQuery, window, document);
