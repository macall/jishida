(function (a) {
    a.fn.promptumenu = function (b) {
        var c = a.extend({
            columns: 3,
            rows: 4,
            direction: 'horizontal',
            width: 'auto',
            height: 'auto',
            duration: 500,
            pages: true,
            inertia: 200
        }, b);
        return this.each(function () {
            var n = a(this);
            var l;
            var p = {
                x: 0,
                y: 1,
                page: 1
            };
            var q = {
                width: 0,
                height: 0,
                pages: 1,
                current_page: 1
            };
            var f = {
                go_to: function (s, v, r) {
                    if (v === undefined) {
                        v = 'swing'
                    }
                    if (r === undefined) {
                        r = false
                    }
                    var u,
                    t;
                    if (c.direction == 'vertical') {
                        u = {
                            top: (s - 1) * l.height * ( - 1)
                        };
                        t = {
                            '-webkit-transform': 'translate3d(0px, ' + ((s - 1) * l.height * ( - 1)) + 'px, 0px)'
                        }
                    } else {
                        u = {
                            left: (s - 1) * l.width * ( - 1)
                        };
                        t = {
                            '-webkit-transform': 'translate3d(' + ((s - 1) * l.width * ( - 1)) + 'px, 0px, 0px)'
                        }
                    }
                    if (r) {
                        n.css({
                            '-webkit-transition-property': '-webkit-transform',
                            '-webkit-transition-duration': c.duration + 'ms',
                            '-webkit-transition-timing-function': 'ease-out'
                        });
                        n.css(t);
                        n.data('ppos', (s - 1) * l.width * ( - 1))
                    } else {
                        n.animate(u, c.duration, v)
                    }
                    n.parent('.promptumenu_window') .find('.promptumenu_nav a.active') .removeClass('active');
                    n.parent('.promptumenu_window') .find('.promptumenu_nav a:nth-child(' + (s) + ')') .addClass('active');
                    q.current_page = s
                },
                next_page: function () {
                    f.go_to(q.current_page + 1)
                },
                prev_page: function () {
                    f.go_to(q.current_page - 1)
                }
            };
            if (n.data('promptumenu')) {
                console.error('You are calling promptumenu for an element more than twice. Please have a look.')
            } else {
                n.data('promptumenu', true);
                n.data('ppos', 0);
                l = {
                    width: (c.width == 'auto') ? n.width()  : c.width,
                    height: (c.height == 'auto' || c.height > n.height()) ? n.height()  : c.height,
                    padding: 0,
                    display: 'none',
                    overflow: 'hidden'
                };
                q.width = l.width / c.columns;
                q.height = l.height / c.rows;
                n.wrap('<div class="promptumenu_window" />');
                n.parent('.promptumenu_window') .css(l);
                n.css({
                    display: 'block',
                    position: 'absolute',
                    'list-style': 'none',
                    overflow: 'hidden',
                    height: 'auto',
                    width: 'auto'
                });
                n.children('li') .each(function () {
                    var r = a(this);
                    p.x += 1;
                    if (p.x > c.columns) {
                        p.x = 1;
                        p.y += 1
                    }
                    if (p.y > c.rows) {
                        p.x = 1;
                        p.y = 1;
                        p.page += 1
                    }
                    r.data('layout', a.extend({
                    }, p));
                    if (c.direction == 'vertical') {
                        r.css({
                            top: Math.round((p.y * q.height - q.height / 2) - (r.height() / 2) + (p.page - 1) * l.height),
                            left: Math.round((p.x * q.width - q.width / 2) - (r.width() / 2))
                        });
                        r.find('img') .bind('load', function () {
                            var s = r.data('layout');
                            r.css({
                                top: Math.round((s.y * q.height - q.height / 2) - (r.height() / 2) + (s.page - 1) * l.height),
                                left: Math.round((s.x * q.width - q.width / 2) - (r.width() / 2))
                            })
                        })
                    } else {
                        r.css({
                            top: Math.round((p.y * q.height - q.height / 2) - (r.height() / 2)),
                            left: Math.round((p.x * q.width - q.width / 2) - (r.width() / 2) + (p.page - 1) * l.width)
                        });
                        r.find('img') .bind('load', function () {
                            var s = r.data('layout');
                            r.css({
                                top: Math.round((s.y * q.height - q.height / 2) - (r.height() / 2)),
                                left: Math.round((s.x * q.width - q.width / 2) - (r.width() / 2) + (s.page - 1) * l.width)
                            })
                        })
                    }
                });
                q.pages = p.page;
                n.data('promptumenu_page_count', q.pages);
                if (q.pages > 1 && c.pages == true) {
                    var e = '<a class="active">Page 1</a>';
                    for (i = 2; i <= q.pages; i++) {
                        e = e + '<a>Page ' + i + '</a>'
                    }
                    n.parent('div.promptumenu_window') .append('<div class="promptumenu_nav">' + e + '</div>');
                    n.parent('div.promptumenu_window') .find('.promptumenu_nav a') .bind('click.promptumenu', function () {
                        f.go_to(a(this) .index() + 1)
                    })
                }
                n.bind('mousedown.promptumenu', function (s) {
                    s.preventDefault();
                    n.stop(true, false);
                    var u = n.position();
                    var t = {
                        x: s.pageX,
                        y: s.pageY
                    };
                    var v = {
                        x: 0,
                        y: 0
                    };
                    var r = new Array();
                    a(document) .bind('mousemove.promptumenu', function (y) {
                        y.preventDefault();
                        var x = new Date();
                        var w = {
                            time: x.getTime(),
                            x: y.pageX,
                            y: y.pageY
                        };
                        while (r.length > 4) {
                            r.shift()
                        }
                        if (c.direction == 'vertical') {
                            v.y = y.pageY - t.y;
                            n.css('top', u.top + v.y)
                        } else {
                            v.x = y.pageX - t.x;
                            n.css('left', u.left + v.x)
                        }
                        r.push(w)
                    });
                    a(document) .bind('mouseup.promptumenu', function (y) {
                        y.preventDefault();
                        a(document) .unbind('.promptumenu');
                        var w = new Date();
                        var x = r[0];
                        var D = {
                            time: w.getTime(),
                            x: y.pageX,
                            y: y.pageY
                        };
                        var z = {
                            time: (D.time - x.time),
                            x: (D.x - x.x),
                            y: (D.y - x.y)
                        };
                        var A = {
                            x: z.x / z.time,
                            y: z.y / z.time
                        };
                        if (c.direction == 'vertical') {
                            var C = u.top + v.y + A.y * c.inertia;
                            relpages = n.height() / l.height;
                            if (C < (( - 1) * l.height * (relpages - 1))) {
                                C = ( - 1) * l.height * (relpages - 1)
                            } else {
                                if (C > 0) {
                                    C = 0
                                }
                            }
                            if (c.pages) {
                                var B = Math.round(( - C) / l.height);
                                f.go_to(B + 1, 'inertia')
                            } else {
                                n.animate({
                                    top: C
                                }, Math.abs(A.y * c.inertia), 'inertia')
                            }
                        } else {
                            var C = u.left + v.x + A.x * c.inertia;
                            if (C < (( - 1) * l.width * (q.pages - 1))) {
                                C = ( - 1) * l.width * (q.pages - 1)
                            } else {
                                if (C > 0) {
                                    C = 0
                                }
                            }
                            if (c.pages) {
                                var B = Math.round(( - C) / l.width);
                                f.go_to(B + 1, 'inertia')
                            } else {
                                n.animate({
                                    left: C
                                }, Math.abs(A.x * c.inertia), 'inertia')
                            }
                        }
                    })
                });
                try {
                    var d,
                    k,
                    j;
                    var g = new Array();
                    var h = function (t) {
                        t.preventDefault();
                        var s = new Date();
                        var r = {
                            time: s.getTime(),
                            x: t.touches[0].pageX,
                            y: t.touches[0].pageY
                        };
                        while (g.length > 4) {
                            g.shift()
                        }
                        if (c.direction == 'vertical') {
                            j.y = t.touches[0].pageY - k.y;
                            n.css('-webkit-transform', 'translate3d(0px, ' + (d + j.y) + 'px, 0px)')
                        } else {
                            j.x = t.touches[0].pageX - k.x;
                            n.css('-webkit-transform', 'translate3d(' + (d + j.x) + 'px, 0px, 0px)')
                        }
                        g.push(r)
                    };
                    var o = function (s) {
                        document.removeEventListener('touchmove', h, false);
                        document.removeEventListener('touchend', o, false);
                        var r = g[0];
                        var x = g[g.length - 1];
                        var t = {
                            time: (x.time - r.time),
                            x: (x.x - r.x),
                            y: (x.y - r.y)
                        };
                        var u = {
                            x: t.x / t.time,
                            y: t.y / t.time
                        };
                        if (c.direction == 'vertical') {
                            if (isNaN(u.y)) {
                                u.y = 2
                            }
                            n.css({
                                '-webkit-transition-duration': Math.abs(u.y * c.inertia * 3) + 'ms',
                                '-webkit-transition-timing-function': 'ease-out'
                            });
                            var w = d + j.y + u.y * c.inertia;
                            relpages = n.height() / l.height;
                            if (w < (( - 1) * l.height * (relpages - 1))) {
                                w = ( - 1) * l.height * (relpages - 1)
                            } else {
                                if (w > 0) {
                                    w = 0
                                }
                            }
                            if (c.pages) {
                                var v = Math.round(( - w) / l.height);
                                f.go_to(v + 1, 'inertia', true)
                            } else {
                                n.css('-webkit-transform', 'translate3d(0px, ' + w + 'px, 0px)');
                                n.data('ppos', w)
                            }
                        } else {
                            if (isNaN(u.x)) {
                                u.x = 2
                            }
                            n.css({
                                '-webkit-transition-duration': Math.abs(u.y * c.inertia * 3) + 'ms',
                                '-webkit-transition-timing-function': 'ease-out'
                            });
                            var w = d + j.x + u.x * c.inertia;
                            if (w < (( - 1) * l.width * (q.pages - 1))) {
                                w = ( - 1) * l.width * (q.pages - 1)
                            } else {
                                if (w > 0) {
                                    w = 0
                                }
                            }
                            if (c.pages) {
                                var v = Math.round(( - w) / l.width);
                                f.go_to(v + 1, 'inertia', true)
                            } else {
                                n.css('-webkit-transform', 'translate3d(' + w + 'px, 0px, 0px)');
                                n.data('ppos', w)
                            }
                        }
                    };
                    n[0].addEventListener('touchstart', function (s) {
                        n.unbind('.promptumenu');
                        n.stop(true, false);
                        n.css({
                            '-webkit-transition-duration': '0ms'
                        });
                        var r = new Date();
                        d = n.data('ppos');
                        k = {
                            x: s.touches[0].pageX,
                            y: s.touches[0].pageY,
                            time: r.getTime()
                        };
                        j = {
                            x: 0,
                            y: 0
                        };
                        g = new Array();
                        document.addEventListener('touchmove', h, false);
                        document.addEventListener('touchend', o, false);
                        document.addEventListener('touchcancel', o, false)
                    }, false)
                } catch (m) {
                }
            }
        })
    }
}) (jQuery);
jQuery.extend(jQuery.easing, {
    inertia: function (e, f, a, h, g) {
        return h * ((f = f / g - 1) * f * f + 1) + a
    }
});
