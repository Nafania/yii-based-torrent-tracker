(function($){
        $.fn.scrollText = function(action) {
            
            var defaults = {
                step: 1,
                marginLeft: 10,
                marginRight: 10,
                interval: 30
            };
            var options = {};
            
            if(typeof action === 'object') {
                options = action;
                action = "init";
            }
            else {
                action = action || "init";
            }
            var conf = $.extend(defaults, options);
            
            var $self = this;
            if(action == "init") {
                
                return this.each(function() {
                    var data = $(this).data("scrollText"),
                        running = false,
                        timer = null;
                        
                    if(data) {
                        running = data.running;
                        timer = data.timer;
                        
                    }
                    
                    var w_outer = $(this).width(),
                        w_inner = $(this).find(".text-scroll-inner").width(),
                        w_diff = w_inner - w_outer;
                    var need_scroll = (w_diff > 0);
                    
                    if(running && !need_scroll) {
                        // stop running instance
                        $(this).find(".text-scroll-container").css({left: 0});
                        clearInterval(timer);
                        timer = null;
                        running = false;
                    }
                    
                    data = {
                        conf: conf,
                        need_scroll: need_scroll,
                        diff: w_diff,
                        width: w_inner,
                        owidth: w_outer,
                        direction: 0,
                        running: running,
                        timer: timer
                    };
                    
                    $(this).data("scrollText", data);
                });
            }
            else if(action == "start") {
                var state = $self.data("scrollText");
                if( ! state) {
                    return this;
                }
                
                if( ! state.need_scroll) {
                    return this;
                }
                
                if(state.running) {
                    return this;
                }
                
                var $container = $self.find(".text-scroll-container");
                var scrollFunc = function() {
                    $container.css("left",
                        function(index, value) {
                            value = parseInt(value);
                            
                            if(Math.abs(value) >= state.width) {
                                return state.owidth + "px";
                            }
                            else {
                                return (value - state.conf.step) + "px";
                            }
                        });
                };
                
                state.running = true;
                state.timer = setInterval(scrollFunc, state.conf.interval);
                $self.data("scrollText", state);
                return this;
            }
            else if(action == "stop") {
                var state = $self.data("scrollText");
                if( ! state) {
                    return this;
                }
                if( ! state.running) {
                    return this;
                }
                state.running = false;
                clearInterval(state.timer);
                $self.data("scrollText", state);
                return this;
            }
        }
})( jQuery );
