<script src="<?php echo JS; ?>jquery-3.6.0.min.js"></script>
<script src="<?php echo ASSETS; ?>bootstrap/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?php echo PLUGINS; ?>bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="<?php echo PLUGINS; ?>chart.js/Chart.min.js"></script>
<script src="<?php echo JS; ?>off-canvas.js"></script>
<script src="<?php echo JS; ?>hoverable-collapse.js"></script>
<script src="<?php echo JS; ?>template.js"></script>
<script src="<?php echo JS; ?>settings.js"></script>
<!-- <script src="<?php echo JS; ?>dashboard.js"></script> -->
<script src="<?php echo JS; ?>Chart.roundedBarCharts.js"></script>
<script type="text/javascript">
	function headerFixTable(){
        var headerFixTable = document.getElementById("headerFixTable");
        heightWindow=window.outerHeight-100;
        heightTable=headerFixTable.clientHeight+175;

        if (heightTable>heightWindow) {
            heightTem=heightWindow-175;
            headerFixTable.style.height=heightTem+"px";
        }
    }
</script>
<script type="text/javascript">
  function copyToClipboard(element) { 
      var text = $('#'+element).clone().find('br').prepend('\r\n').end().text()
      text=text.replace(/\n\n/g,'\n')
      element = $('<textarea>').appendTo('body').val(text).select() 
      document.execCommand('copy') 
      element.remove() 
  }
</script>