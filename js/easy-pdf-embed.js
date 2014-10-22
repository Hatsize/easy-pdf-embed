(function($){

	// Taken from - http://mozilla.github.io/pdf.js/features/
	var pdfJSTests = [];
	pdfJSTests.push(function checkSupportEventListener() {
		var div = document.createElement('div');
		if (div.addEventListener) {
			return true;
		}
		else {
			return false;
		}
    });
    pdfJSTests.push(function checkSupportCanvas() {
		var isCanvasSupported = (function () {
			try {
				document.createElement('canvas').getContext('2d').fillStyle = '#FFFFFF';
				return true;
			}
			catch (e) {
				return false;
			}
		})();
		if (isCanvasSupported) {
			return true;
		}
		else {
			return false;
		}
    });
    pdfJSTests.push(function checkSupportGetLiteralProperty() {
		try {
			var Test = eval('var Test =  { get t() { return {}; } }; Test');
			Test.t.test = true;
			return true;
		}
		catch (e) {
			return false;
		}
    });

    // True if array of functions all return true
    function allTestsPass(testArray) {
	    var pdfJSSupported = true;
	    var i = 0;
	    for (i = 0; i < testArray.length; i++) {
	    	pdfJSSupported = pdfJSSupported && testArray[i]();
	    }
	    return pdfJSSupported;
    }

    var pdfJSSupported = allTestsPass(pdfJSTests);

    function pdfEmbedResponse() {
    	var $this = $(this);
    	if(pdfJSSupported) {
    		$this.html($this.find('.wp-easy-pdf-embed-pdfjs').html());
    	}
    	else {
    		$this.html($this.find('.wp-easy-pdf-embed-embedpdf').html());
    	}
    }

    $(function main(){
    	$('.wp-easy-pdf-embed').each(pdfEmbedResponse);
    });
})(jQuery);