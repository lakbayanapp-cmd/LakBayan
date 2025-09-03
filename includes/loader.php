
<style>
#lakbayan-loader-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: url('assets/images/Loading Screen BG.png') center center no-repeat;
    background-size: cover;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
#lakbayan-loader-logo {
    width: 180px;
    height: auto;
    margin-bottom: 40px;
}
#lakbayan-loader-bar-bg {
    width: 220px;
    height: 18px;
    background: #eee;
    border-radius: 9px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
#lakbayan-loader-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #FA89CC 0%, #F562C3 100%);
    border-radius: 9px;
    transition: width 0.2s;
}

</style>
<div id="lakbayan-loader-overlay">
    <img id="lakbayan-loader-logo" src="assets/images/LAKBAYAN LOGO.png" alt="Lakbayan Logo">
    <div id="lakbayan-loader-bar-bg">
        <div id="lakbayan-loader-bar"></div>
    </div>
</div>
<script>
(function(){
    var bar = document.getElementById('lakbayan-loader-bar');
    var overlay = document.getElementById('lakbayan-loader-overlay');
    var duration = 2000; // 2 seconds
    var start = null;
    function animateLoader(ts){
        if(!start) start = ts;
        var progress = Math.min((ts - start) / duration, 1);
        bar.style.width = (progress * 100) + '%';
        if(progress < 1){
            requestAnimationFrame(animateLoader);
        } else {
            overlay.style.opacity = '1';
            overlay.style.transition = 'opacity 0.5s';
            overlay.style.opacity = '0';
            setTimeout(function(){
                overlay.parentNode.removeChild(overlay);
            }, 500);
        }
    }
    requestAnimationFrame(animateLoader);
})();
</script> 