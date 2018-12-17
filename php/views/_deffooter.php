
<!-- https://toster.ru/q/19238 -->
<style type="text/css">

</style>
</div>
<footer id="document-footer">
    <div class="container-fluid">
        demonius
    </div>
</footer>
<script type="text/javascript">
    (function(){
        var f = function(){
            var docBody = document.getElementById('document-body');
            var docFooter = document.getElementById('document-footer');
            var docHeader = document.getElementById('document-header');

            $(docBody).height(document.body.clientHeight - docFooter.offsetHeight - docHeader.offsetHeight);
            console.log(window.innerHeight + ' => ' + docFooter.offsetHeight + ' => ' + docBody.offsetHeight)
        };
        window.addEventListener('resize', f)
        f();
    })()
</script>
</body>
</html>
