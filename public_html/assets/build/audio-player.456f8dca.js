"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[845],{651:(e,i,t)=>{var s,l,a,o,r,n,p,d,u,c,m,v,b,y,f,g,h,S,L,q,x,w,T,E;function H(e,i,t){i.split(" ").forEach((function(i){return e.addEventListener(i,t,!1)}))}function M(e){var i,t=0,s=0;return(e=parseInt(e,10))>3600&&(e-=60*(t=parseInt(e/3600,10))*60),e>60&&(e-=60*(s=parseInt(e/60,10))),i=e,e=t>0?t+":":"",e+=s>0?(s<10&&t>0?"0":"")+s+":":"0:",e+=(i<10?"0":"")+i}function k(){var e;(u.querySelector(".start-time").innerHTML=M(o.currentTime),T||(u.querySelector(".end-time").innerHTML=M(o.duration),c.value=o.currentTime/o.duration*100),o.currentTime==o.duration)&&(u.querySelector(".simp-plause").classList.remove("bi-pause-fill"),u.querySelector(".simp-plause").classList.add("bi-play-fill"),o.removeEventListener("timeupdate",k),q?(++S==h.length?(S=0,e=h[0]):e=h[S],_(e),P(S)):L=!1)}function N(){E=4==o.readyState,T="Infinity"==o.duration,u.querySelector(".simp-plause").disabled=!1,c.disabled=!!T,T||(c.parentNode.classList.remove("simp-load","simp-loading"),u.querySelector(".end-time").innerHTML=M(o.duration)),o.addEventListener("timeupdate",k),E&&L&&o.play(),H(c,"touchstart mousedown",(function(e){if(T)return e.stopPropagation(),!1;4==o.readyState&&(o.removeEventListener("timeupdate",k),o.pause())})),H(c,"touchend mouseup",(function(e){if(T)return e.stopPropagation(),!1;4==o.readyState&&(o.currentTime=c.value*o.duration/100,o.addEventListener("timeupdate",k),L&&o.play())}))}function A(e){c.parentNode.classList.add("simp-loading"),u.querySelector(".simp-plause").disabled=!0,o.querySelector("source").src=e.dataset.src,o.load(),o.volume=parseFloat(b/100),o.addEventListener("canplaythrough",N),o.addEventListener("error",(function(){alert("Please reload the page.")}))}function P(e){n.innerHTML=h[e].dataset.cover?'<div style="background:url('+h[e].dataset.cover+') no-repeat;background-size:cover;width:80px;height:80px;"></div>':'<i class="bi bi-music-note-beamed display-2"></i>',p.innerHTML=g[e].querySelector(".simp-source").innerHTML,d.innerHTML=g[e].querySelector(".simp-desc")?g[e].querySelector(".simp-desc").innerHTML:""}function _(e){var i,t;E=!1,u.querySelector(".simp-prev").disabled=0==S,u.querySelector(".simp-plause").disabled=!!a,u.querySelector(".simp-next").disabled=S==h.length-1,c.parentNode.classList.add("simp-load"),c.disabled=!0,c.value=0,u.querySelector(".start-time").innerHTML="00:00",u.querySelector(".end-time").innerHTML="00:00",e=x&&q?h[(i=0,t=h.length-1,i=Math.ceil(i),t=Math.floor(t),Math.floor(Math.random()*(t-i+1))+i)]:e;for(var s=0;s<h.length;s++)h[s].parentNode.classList.remove("simp-active"),h[s]==e&&(S=s,h[s].parentNode.classList.add("simp-active"));var l,o,r,n,p=(l=g[S],o=l.parentNode.getBoundingClientRect(),r=l.getBoundingClientRect(),(n={}).top=r.top-o.top+l.parentNode.scrollTop,n.right=r.right-o.right,n.bottom=r.bottom-o.bottom,n.left=r.left-o.left,n);g[S].parentNode.scrollTop=p.top,(a||L)&&A(e),L&&(u.querySelector(".simp-plause").classList.remove("bi-play-fill"),u.querySelector(".simp-plause").classList.add("bi-pause-fill"))}t(933),window.audioPlayer=function(){if(document.querySelector("#simp")){s=document.querySelector("#simp"),f=s.querySelector(".simp-playlist"),g=f.querySelectorAll("li"),h=f.querySelectorAll("[data-src]"),S=0,L=!1,q=!1,x=!1,w=!1,T=!1,E=!1,l=s.dataset.config?JSON.parse(s.dataset.config):{shide_top:!1,shide_btm:!1,auto_load:!1};'<audio id="audio" preload><source src="" type="audio/mpeg"></audio>','<div class="simp-display"><div class="simp-album w-full flex-wrap"><div class="simp-cover"><i class="bi bi-music-note-beamed display-2"></i></div><div class="simp-info"><div class="simp-title">Title</div><div class="simp-artist">Artist</div></div></div></div>','<div class="simp-controls flex-wrap flex-align">','<div class="simp-plauseward flex flex-align"><button type="button" class="simp-prev bi bi-rewind-fill" disabled></button><button type="button" class="simp-plause bi bi-play-fill" disabled></button><button type="button" class="simp-next bi bi-fast-forward-fill" disabled></button></div>','<div class="simp-tracker simp-load"><input class="simp-progress" type="range" min="0" max="100" value="0" disabled/><div class="simp-buffer"></div></div>','<div class="simp-time flex flex-align"><span class="start-time">00:00</span><span class="simp-slash">&#160;/&#160;</span><span class="end-time">00:00</span></div>','<div class="simp-volume flex flex-align"><button type="button" class="simp-mute bi bi-volume-up-fill"></button><input class="simp-v-slider" type="range" min="0" max="100" value="100"/></div>','<div class="simp-others flex flex-align"><button type="button" class="simp-plext bi bi-play-circle" title="Auto Play"></button><button type="button" class="simp-random bi bi-shuffle" title="Random"></button><div class="simp-shide"><button type="button" class="simp-shide-top bi bi-caret-up-fill" title="Show/Hide Album"></button><button type="button" class="simp-shide-bottom bi bi-caret-down-fill" title="Show/Hide Playlist"></button></div></div>',"</div>";var e=document.createElement("div");e.classList.add("simp-player"),e.innerHTML='<audio id="audio" preload><source src="" type="audio/mpeg"></audio><div class="simp-display"><div class="simp-album w-full flex-wrap"><div class="simp-cover"><i class="bi bi-music-note-beamed display-2"></i></div><div class="simp-info"><div class="simp-title">Title</div><div class="simp-artist">Artist</div></div></div></div><div class="simp-controls flex-wrap flex-align"><div class="simp-plauseward flex flex-align"><button type="button" class="simp-prev bi bi-rewind-fill" disabled></button><button type="button" class="simp-plause bi bi-play-fill" disabled></button><button type="button" class="simp-next bi bi-fast-forward-fill" disabled></button></div><div class="simp-tracker simp-load"><input class="simp-progress" type="range" min="0" max="100" value="0" disabled/><div class="simp-buffer"></div></div><div class="simp-time flex flex-align"><span class="start-time">00:00</span><span class="simp-slash">&#160;/&#160;</span><span class="end-time">00:00</span></div><div class="simp-volume flex flex-align"><button type="button" class="simp-mute bi bi-volume-up-fill"></button><input class="simp-v-slider" type="range" min="0" max="100" value="100"/></div><div class="simp-others flex flex-align"><button type="button" class="simp-plext bi bi-play-circle" title="Auto Play"></button><button type="button" class="simp-random bi bi-shuffle" title="Random"></button><div class="simp-shide"><button type="button" class="simp-shide-top bi bi-caret-up-fill" title="Show/Hide Album"></button><button type="button" class="simp-shide-bottom bi bi-caret-down-fill" title="Show/Hide Playlist"></button></div></div></div>',s.insertBefore(e,f),s=document.querySelector("#simp"),o=s.querySelector("#audio"),r=s.querySelector(".simp-album"),n=r.querySelector(".simp-cover"),p=r.querySelector(".simp-title"),d=r.querySelector(".simp-artist"),u=s.querySelector(".simp-controls"),c=u.querySelector(".simp-progress"),m=u.querySelector(".simp-volume"),v=m.querySelector(".simp-v-slider"),b=v.value,y=u.querySelector(".simp-others"),a=l.auto_load,l.shide_top&&r.parentNode.classList.toggle("simp-hide"),l.shide_btm&&(f.classList.add("simp-display"),f.classList.toggle("simp-hide")),h.length<=1&&(u.querySelector(".simp-prev").style.display="none",u.querySelector(".simp-next").style.display="none",y.querySelector(".simp-plext").style.display="none",y.querySelector(".simp-random").style.display="none"),g.forEach((function(e,i){e.classList.contains("simp-active")&&(S=i),e.addEventListener("click",(function(){o.removeEventListener("timeupdate",k),S=i,_(this.querySelector(".simp-source")),P(S)}))})),_(h[S]),P(S),u.querySelector(".simp-plauseward").addEventListener("click",(function(e){var i=e.target.classList;i.contains("simp-plause")?o.paused?(E||A(h[S]),o.play(),L=!0,i.remove("bi-play-fill"),i.add("bi-pause-fill")):(o.pause(),L=!1,i.remove("bi-pause-fill"),i.add("bi-play-fill")):(i.contains("simp-prev")&&0!=S?(S-=1,e.target.disabled=0==S):i.contains("simp-next")&&S!=h.length-1&&(S+=1,e.target.disabled=S==h.length-1),o.removeEventListener("timeupdate",k),_(h[S]),P(S))})),m.addEventListener("click",(function(e){var i=e.target.classList;i.contains("simp-mute")?i.contains("bi-volume-up-fill")?(i.remove("bi-volume-up-fill"),i.add("bi-volume-mute-fil"),v.value=0):(i.remove("bi-volume-mute-fill"),i.add("bi-volume-up-fill"),v.value=b):0!=(b=v.value)&&(u.querySelector(".simp-mute").classList.remove("bi-volume-mute-fill"),u.querySelector(".simp-mute").classList.add("bi-volume-up-fill")),o.volume=parseFloat(v.value/100)})),y.addEventListener("click",(function(e){var i=e.target.classList;i.contains("simp-plext")?(q=!(q&&!x),x||(w=!w),i.contains("simp-active")&&!x?i.remove("simp-active"):i.add("simp-active")):i.contains("simp-random")?(x=!x,q&&!w?(q=!1,y.querySelector(".simp-plext").classList.remove("simp-active")):(q=!0,y.querySelector(".simp-plext").classList.add("simp-active")),i.contains("simp-active")?i.remove("simp-active"):i.add("simp-active")):i.contains("simp-shide-top")?r.parentNode.classList.toggle("simp-hide"):i.contains("simp-shide-bottom")&&(f.classList.add("simp-display"),f.classList.toggle("simp-hide"))}))}}},933:(e,i,t)=>{t.r(i)}},e=>{var i;i=651,e(e.s=i)}]);