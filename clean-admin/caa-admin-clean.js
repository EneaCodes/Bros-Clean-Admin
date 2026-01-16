(function () {
    function text(el) {
        return (el.innerText || el.textContent || "").toLowerCase();
    }

    function runClean() {
        var opts = window.CAA_OPTIONS || {};

        var promoWords = Array.isArray(opts.promoWords) ? opts.promoWords : [];
        var reviewWords = Array.isArray(opts.reviewWords) ? opts.reviewWords : [];

        promoWords = promoWords.map(function (w) {
            return String(w).toLowerCase();
        });

        reviewWords = reviewWords.map(function (w) {
            return String(w).toLowerCase();
        });

        var url = window.location.href;
        var isPluginsPage = /plugins|elementor|elementskit|settings/i.test(url);

        var allNotices = document.querySelectorAll(
            ".notice, .updated, .update-nag, .welcome-panel, .wrap .postbox, .plugin-card"
        );

        allNotices.forEach(function (el) {
            var t = text(el);

            if (
                t.indexOf("error:") !== -1 ||
                t.indexOf("warning:") !== -1 ||
                el.classList.contains("notice-error") ||
                el.classList.contains("notice-warning")
            ) {
                return;
            }

            var shouldHide = false;

            if (
                (opts.hide_dashboard_ads && window.pagenow === "dashboard") ||
                (opts.hide_plugin_promos && isPluginsPage)
            ) {
                for (var i = 0; i < promoWords.length; i++) {
                    if (t.indexOf(promoWords[i]) !== -1) {
                        shouldHide = true;
                        break;
                    }
                }
            }

            if (!shouldHide && opts.hide_review_nags) {
                for (var j = 0; j < reviewWords.length; j++) {
                    if (t.indexOf(reviewWords[j]) !== -1) {
                        shouldHide = true;
                        break;
                    }
                }
            }

            if (shouldHide) {
                el.style.display = "none";
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        runClean();
        setTimeout(runClean, 1500);
        setTimeout(runClean, 4000);
    });
})();