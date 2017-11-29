! function(t, e) {
    "object" == typeof module && module.exports ? (module.exports = e(), module.exports.default = module.exports) : t.timeago = e()
}("undefined" != typeof window ? window : this, function() {
    function t(t) {
        return t instanceof Date ? t : isNaN(t) ? /^\d+$/.test(t) ? new Date(e(t)) : (t = (t || "").trim().replace(/\.\d+/, "").replace(/-/, "/").replace(/-/, "/").replace(/(\d)T(\d)/, "$1 $2").replace(/Z/, " UTC").replace(/([\+\-]\d\d)\:?(\d\d)/, " $1$2"), new Date(t)) : new Date(e(t))
    }

    function e(t) {
        return parseInt(t)
    }

    function n(t, n, r) {
        n = p[n] ? n : p[r] ? r : "en";
        for (var o = 0, i = t < 0 ? 1 : 0, a = t = Math.abs(t); t >= h[o] && o < m; o++) t /= h[o];
        return t = e(t), o *= 2, t > (0 === o ? 9 : 1) && (o += 1), p[n](t, o, a)[i].replace("%s", t)
    }

    function r(e, n) {
        return ((n = n ? t(n) : new Date) - t(e)) / 1e3
    }

    function o(t) {
        for (var e = 1, n = 0, r = Math.abs(t); t >= h[n] && n < m; n++) t /= h[n], e *= h[n];
        return r %= e, r = r ? e - r : e, Math.ceil(r)
    }

    function i(t) {
        return t.dataset.timeago ? t.dataset.timeago : a(t, w)
    }

    function a(t, e) {
        return t.getAttribute ? t.getAttribute(e) : t.attr ? t.attr(e) : void 0
    }

    function u(t, e) {
        return t.setAttribute ? t.setAttribute(_, e) : t.attr ? t.attr(_, e) : void 0
    }

    function c(t) {
        return a(t, _)
    }

    function d(t, e) {
        this.nowDate = t, this.defaultLocale = e || "en"
    }

    function f(t, e) {
        return new d(t, e)
    }
    var s = "second_minute_hour_day_week_month_year".split("_"),
        l = "秒_分钟_小时_天_周_月_年".split("_"),
        p = {
            en: function(t, e) {
                if (0 === e) return ["just now", "right now"];
                var n = s[parseInt(e / 2)];
                return t > 1 && (n += "s"), [t + " " + n + " ago", "in " + t + " " + n]
            },
            zh_CN: function(t, e) {
                if (0 === e) return ["刚刚", "片刻后"];
                var n = l[parseInt(e / 2)];
                return [t + n + "前", t + n + "后"]
            }
        },
        h = [60, 60, 24, 7, 365 / 7 / 12, 12],
        m = 6,
        w = "datetime",
        _ = "data-tid",
        v = {};
    return d.prototype.doRender = function(t, e, i) {
        var a, c = r(e, this.nowDate),
            d = this;
        t.innerHTML = n(c, i, this.defaultLocale), v[a = setTimeout(function() {
            d.doRender(t, e, i), delete v[a]
        }, Math.min(1e3 * o(c), 2147483647))] = 0, u(t, a)
    }, d.prototype.format = function(t, e) {
        return n(r(t, this.nowDate), e, this.defaultLocale)
    }, d.prototype.render = function(t, e) {
        void 0 === t.length && (t = [t]);
        for (var n = 0, r = t.length; n < r; n++) this.doRender(t[n], i(t[n]), e)
    }, d.prototype.setLocale = function(t) {
        this.defaultLocale = t
    }, f.register = function(t, e) {
        p[t] = e
    }, f.cancel = function(t) {
        var e;
        if (t)(e = c(t)) && (clearTimeout(e), delete v[e]);
        else {
            for (e in v) clearTimeout(e);
            v = {}
        }
    }, f
});