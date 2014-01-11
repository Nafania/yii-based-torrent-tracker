<?php

class AnalyticsWidget extends CWidget {
	public $li = true;
	public $yaId;
	public $gaId;
	public $gaDomain;

	public function init () {
		if ( !$this->yaId ) {
			$this->yaId = Yii::app()->getConfig()->get('analyticsWidget.yaId');
		}
		if ( !$this->gaId ) {
			$this->gaId = Yii::app()->getConfig()->get('analyticsWidget.gaId');
		}
		if ( !$this->gaDomain ) {
			$this->gaDomain = Yii::app()->getConfig()->get('analyticsWidget.gaDomain');
		}
	}

	public function run () {

		$cs = Yii::app()->getClientScript();

		if ( $this->li ) {
			$cs->registerScript('liveinternet',
				'(function() {
							var sc = document.createElement("img");
							sc.type = "text/javascript";
							sc.async = true;
							sc.style = "display:none;";
							sc.src = "//counter.yadro.ru/hit?t26.6;r" + escape(document.referrer) + ((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ";u" + escape(document.URL) +";h"+escape(document.title.substring(0,80)) + ";" + Math.random();
							var s = document.getElementsByTagName("script")[0];
							s.parentNode.insertBefore(sc, s);
						})();',
				CClientScript::POS_END);
		}

		if ( $this->yaId ) {
			$cs->registerScript('yandexMetrika',
				'(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter' . $this->yaId . ' = new Ya.Metrika({id:' . $this->yaId . ', webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true, trackHash:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");',
				CClientScript::POS_END);
		}

		if ( $this->gaId && $this->gaDomain ) {
			$cs->registerScript('googleAnalytics',
				"		  var _gaq = _gaq || [];
								  _gaq.push(['_setAccount', '" . $this->gaId . "']);
							  _gaq.push(['_setDomainName', '" . $this->gaDomain . "']);
							  _gaq.push(['_setAllowLinker', true]);
							  _gaq.push(['_trackPageview']);							
							  (function() {
								var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
								ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
								var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
							  })();",
				CClientScript::POS_END);
		}
	}
}