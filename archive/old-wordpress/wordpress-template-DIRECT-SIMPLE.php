<?php
/*
Template Name: AI Visa Eligibility Checker - SIMPLE DIRECT IFRAME
*/
get_header();
?>

<link rel="preload" href="https://fonts.googleapis.com/css2?family=Cantora+One&family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="dns-prefetch" href="//vercel.app">
<link rel="preconnect" href="https://gofly-visa-eligibility-checker.vercel.app" crossorigin>
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cantora+One&family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

<style>
:root{
 --mint:#10b981; --orange:#f59e0b; --gold:#fbbf24; --mint-light:rgba(16,185,129,0.08);
 --gradient:linear-gradient(135deg,var(--mint) 0%,var(--orange) 50%,var(--gold) 100%);
 --gradient-reverse:linear-gradient(135deg,var(--gold) 0%,var(--orange) 50%,var(--mint) 100%);
 --bg:#f8fafc; --dark:#1e293b; --success:#059669; --glass:rgba(255,255,255,0.85);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:auto !important}
body{font-family:'Inter',sans-serif;line-height:1.6;color:var(--dark);overflow-x:hidden}
.container{max-width:1200px;margin:0 auto;padding:0 15px}

.progress-container{position:fixed;top:0;left:0;width:100%;height:4px;z-index:9999;background:var(--mint-light);overflow:hidden}
.progress-bar{height:100%;background:var(--gradient);transform:translateX(-100%);transition:transform .1s ease-out}

.lang-switcher{
 position:fixed;top:120px;left:16px;z-index:1000;background:var(--glass);backdrop-filter:blur(20px);
 border:1px solid rgba(255,255,255,0.6);border-radius:25px;padding:6px;box-shadow:0 8px 32px rgba(0,0,0,0.12);
}
.lang-btn{display:block;border:0;background:none;padding:8px 14px;font-weight:600;font-size:11px;cursor:pointer;border-radius:20px;
 transition:all .25s ease;color:var(--dark)!important;}
.lang-btn.active{background:var(--gradient);color:#fff!important;box-shadow:0 4px 12px rgba(16,185,129,0.3);}

.hero{position:relative;background:var(--gradient);color:#fff;text-align:center;overflow:hidden;padding:50px 18px 40px}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 20%,rgba(255,255,255,0.15)0%,transparent 60%);opacity:.2}
.hero::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:var(--gradient-reverse)}
.hero-content{max-width:820px;margin:0 auto;position:relative;z-index:2}

h1,h2,h3,h4{font-family:'Cantora One',sans-serif!important;font-weight:400!important;letter-spacing:-0.015em;text-transform:none!important;line-height:1.02!important}
h1{font-size:clamp(2rem,5.5vw,2.95rem)!important;margin-bottom:16px}
@media (min-width:992px) {h1{font-size:2.85rem!important;letter-spacing:-0.025em!important}}
h2.section-title{font-size:clamp(1.75rem,4.2vw,2.45rem)!important;margin-bottom:40px}
h3{font-size:1.4rem!important;margin:0 0 12px;font-weight:600!important}
.section-title{text-align:center;background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

/* DIRECT IFRAME - NO LOADING OVERLAY */
.tool-container{padding:0;margin:-45px auto 65px;max-width:1100px;position:relative}
.tool-box{
 background:var(--glass);border-radius:28px;height:700px;overflow:hidden;
 box-shadow:0 50px 100px rgba(16,185,129,0.25);border:2px solid rgba(16,185,129,0.2);
 backdrop-filter:blur(10px);transition:all .4s ease;position:relative;
}
.tool-box:hover{box-shadow:0 60px 120px rgba(16,185,129,0.35);border-color:var(--mint);}

/* DIRECT VISIBLE IFRAME */
.tool-box iframe{
 width:100% !important;
 height:100% !important;
 border:none !important;
 display:block !important;
 opacity:1 !important;
 visibility:visible !important;
}

.section{padding:65px 0}.section.alt{background:linear-gradient(180deg,var(--bg) 0%,rgba(16,185,129,0.02) 100%)}
.section.gold{background:linear-gradient(135deg,rgba(251,191,36,0.03) 0%,var(--bg) 50%)}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-top:30px}
.card{background:var(--glass);padding:35px;border-radius:24px;border:1px solid var(--mint-light);box-shadow:0 15px 40px rgba(16,185,129,0.1);
 transition:all .4s ease;backdrop-filter:blur(10px);position:relative;overflow:hidden;}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:var(--gradient)}
.card:hover{transform:translateY(-8px);box-shadow:0 30px 70px rgba(16,185,129,0.2)}

.step-card{position:relative;padding-left:80px}
.step-number{position:absolute;left:22px;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;
 background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;
 box-shadow:0 10px 25px rgba(16,185,129,0.4);border:3px solid #fff;}

.trust-badges{background:var(--gradient);color:#fff;padding:35px 0;text-align:center;position:relative;overflow:hidden}
.trust-title{font-size:clamp(1.4rem,3.5vw,2.1rem);font-weight:400!important;margin-bottom:20px}
.trust-stats{display:flex;justify-content:center;gap:30px;flex-wrap:wrap;font-size:clamp(1.4rem,3.8vw,1.9rem);font-weight:900;margin-top:15px}
.trust-stats div{min-width:110px;text-align:center;line-height:1.2;position:relative}
.trust-stats div::after{content:'';position:absolute;bottom:-8px;left:50%;transform:translateX(-50%);width:30px;height:3px;background:var(--gradient-reverse)}
.trust-label{font-size:.75em;font-weight:500;opacity:.92;display:block;margin-top:4px}
.hotline{font-size:1.1rem;font-weight:700;margin-top:15px;padding:12px 24px;background:var(--glass);border-radius:50px;
 display:inline-block;box-shadow:0 8px 25px rgba(0,0,0,0.15);border:2px solid rgba(255,255,255,0.3);transition:all .3s;color:#000000 !important;}

.eligibility-cta{text-align:center;margin:50px 0;padding:35px 25px;background:linear-gradient(135deg,var(--gold),var(--orange));
 border-radius:28px;box-shadow:0 35px 90px rgba(251,191,36,0.4);position:relative;overflow:hidden;border:2px solid rgba(255,255,255,0.2);
 backdrop-filter:blur(10px);}
.eligibility-btn{display:inline-flex;align-items:center;justify-content:center;gap:12px;padding:22px 48px;font-weight:800;font-size:16px;
 background:linear-gradient(135deg,#fff 0%,rgba(255,255,255,0.9) 100%);color:var(--dark)!important;text-decoration:none;border-radius:60px;
 box-shadow:0 20px 50px rgba(255,255,255,0.5),0 12px 35px rgba(251,191,36,0.3);border:3px solid rgba(255,255,255,0.4);
 transition:all .5s ease;min-height:64px;letter-spacing:0.5px;position:relative;overflow:hidden;}

.disclaimer-box{background:var(--glass);padding:30px 35px;border-radius:20px;border:1px solid var(--mint-light);text-align:center;
 margin:50px auto;max-width:950px;box-shadow:0 20px 50px rgba(16,185,129,0.08);backdrop-filter:blur(15px);}
.disclaimer-box h3{color:var(--mint);margin-bottom:15px;font-size:1.25rem!important;font-weight:700!important}
.disclaimer-box p{font-size:0.98rem;color:#555;line-height:1.55;margin:0}

@media(max-width:768px){
 body{padding-top:155px}.lang-switcher{top:110px;left:14px;padding:6px}
 .hero{padding:42px 14px 35px}.hero h1{font-size:clamp(1.85rem,6vw,2.5rem)!important}
 .tool-container{margin:-35px auto 55px}.tool-box{height:650px;border-radius:24px}
 .cards{gap:20px}.card{padding:28px}.section{padding:55px 0}
 .step-card{padding-left:65px}.step-number{left:15px;width:38px;height:38px;font-size:1.1rem}
 .trust-badges{padding:28px 0}.trust-title{font-size:1.6rem!important}
 .trust-stats{flex-direction:column;gap:25px;font-size:1.6rem}
 .eligibility-cta{margin:40px 10px;padding:28px 20px;border-radius:24px}
 .eligibility-btn{padding:18px 36px;font-size:15px;min-height:58px;gap:8px}
 .disclaimer-box{margin:40px 15px;padding:25px;border-radius:18px}
 .hotline{font-size:1rem;padding:10px 20px;color:#000000 !important;}
}
@media(max-width:480px){
 body{padding-top:148px}.lang-switcher{top:102px;left:12px}
 .hero{padding:38px 12px 32px}.tool-box{height:620px;border-radius:20px}
 .step-card{padding-left:60px}.step-number{left:12px;width:34px;height:34px;font-size:1rem}
 .eligibility-btn{padding:16px 28px;font-size:14px}
}
</style>

<div class="progress-container"><div class="progress-bar"></div></div>

<div class="lang-switcher" role="radiogroup" aria-label="Language selection">
 <button class="lang-btn active" data-lang="en" aria-label="Switch to English">EN</button>
 <button class="lang-btn" data-lang="bn" aria-label="বাংলায় সুইচ করুন">বাংলা</button>
</div>

<section class="hero">
 <div class="container">
  <div class="hero-content">
   <div class="lang-en" style="display:block">
    <h1>Check Visa Options & Difficulty for 40+ Countries Instantly</h1>
    <p style="font-size:clamp(1.05rem,2.6vw,1.45rem);opacity:.97;margin:0 0 32px;line-height:1.4;font-weight:500">
     <strong style="font-weight:700">Free Tool: See Categories, Processing Time & Expert Advice in Seconds</strong><br>
     <span style="font-size:.92rem;display:block;margin-top:8px;opacity:0.95;font-weight:500">
      Quick Service (Visa on Arrival/eVisa) • Premium (Full Documents) • Tourist • Business
     </span>
    </p>
   </div>
   <div class="lang-bn" style="display:none">
    <h1>৪০+ দেশের ভিসা অপশন ও জটিলতা তাৎক্ষণিক চেক করুন</h1>
    <p style="font-size:clamp(1.05rem,2.6vw,1.45rem);opacity:.97;margin:0 0 32px;line-height:1.4;font-weight:500">
     <strong style="font-weight:700">ফ্রি টুল: ক্যাটাগরি, প্রসেসিং সময় ও এক্সপার্ট পরামর্শ সেকেন্ডে দেখুন</strong><br>
     <span style="font-size:.92rem;display:block;margin-top:8px;opacity:0.95;font-weight:500">
      দ্রুত সার্ভিস (Visa on Arrival/eVisa) • প্রিমিয়াম (পূর্ণ ডকুমেন্ট) • টুরিস্ট • বিজনেস
     </span>
    </p>
   </div>
  </div>
 </div>
</section>

<!-- DIRECT IFRAME - NO LOADING OVERLAY -->
<div class="container tool-container">
 <div class="tool-box">
  <iframe src="https://gofly-visa-eligibility-checker.vercel.app/"></iframe>
 </div>
</div>

<div class="container">
 <div class="eligibility-cta">
  <div class="lang-en" style="display:block">
   <a href="https://goflybd.com/visa-processing-agency-in-bangladesh/" target="_blank" class="eligibility-btn">
    👉 Premium Visa Processing Service - 90%+ Success Rate
   </a>
  </div>
  <div class="lang-bn" style="display:none">
   <a href="https://goflybd.com/visa-processing-agency-in-bangladesh/" target="_blank" class="eligibility-btn">
    👉 প্রিমিয়াম ভিসা প্রসেসিং সার্ভিস - ৯০%+ সাফল্য
   </a>
  </div>
 </div>
</div>

<section class="section alt">
 <div class="container">
  <div class="lang-en" style="display:block">
   <h2 class="section-title">How Our Visa Checker Works</h2>
   <div class="cards">
    <div class="card step-card">
     <span class="step-number">1</span>
     <h3>Select Country</h3>
     <p style="font-weight:500">Choose your destination from 40+ countries instantly</p>
    </div>
    <div class="card step-card">
     <span class="step-number">2</span>
     <h3>See Category & Processing Time</h3>
     <p style="font-weight:500">Quick Service (Visa on Arrival/eVisa) vs Premium (Full Documents)</p>
    </div>
    <div class="card step-card">
     <span class="step-number">3</span>
     <h3>Get Expert Advice</h3>
     <p style="font-weight:500">WhatsApp consultation with visa specialists instantly</p>
    </div>
   </div>
  </div>
  <div class="lang-bn" style="display:none">
   <h2 class="section-title">আমাদের ভিসা চেকার কিভাবে কাজ করে</h2>
   <div class="cards">
    <div class="card step-card">
     <span class="step-number">১</span>
     <h3>দেশ সিলেক্ট করুন</h3>
     <p style="font-weight:500">৪০+ দেশের মধ্যে থেকে আপনার গন্তব্য নির্বাচন করুন</p>
    </div>
    <div class="card step-card">
     <span class="step-number">২</span>
     <h3>ক্যাটাগরি ও সময় দেখুন</h3>
     <p style="font-weight:500">দ্রুত সার্ভিস (Visa on Arrival/eVisa) বনাম প্রিমিয়াম (পূর্ণ ডকুমেন্ট)</p>
    </div>
    <div class="card step-card">
     <span class="step-number">৩</span>
     <h3>এক্সপার্ট পরামর্শ পান</h3>
     <p style="font-weight:500">ভিসা বিশেষজ্ঞদের সাথে তাৎক্ষণিক WhatsApp পরামর্শ</p>
    </div>
   </div>
  </div>
 </div>
</section>

<section class="trust-badges">
 <div class="container">
  <div class="lang-en" style="display:block">
   <h2 class="trust-title">Trusted by Thousands Since 2017</h2>
  </div>
  <div class="lang-bn" style="display:none">
   <h2 class="trust-title">২০১৭ থেকে হাজারোর বিশ্বাসী</h2>
  </div>
  <div class="trust-stats">
   <div class="lang-en" style="display:block"><div>10K+<br><span class="trust-label">Cases Analyzed</span></div></div>
   <div class="lang-bn" style="display:none"><div>১০K+<br><span class="trust-label">কেস বিশ্লেষিত</span></div></div>
   <div>90%+<br><span class="trust-label lang-en" style="display:inline">Success Rate*</span><span class="trust-label lang-bn" style="display:none">সাফল্যের হার*</span></div>
   <div>40+<br><span class="trust-label lang-en" style="display:inline">Countries</span><span class="trust-label lang-bn" style="display:none">দেশ</span></div>
   <div>4.8★<br><span class="trust-label">Google Reviews</span></div>
   <div>30s<br><span class="trust-label lang-en" style="display:inline">Results</span><span class="trust-label lang-bn" style="display:none">রেজাল্ট</span></div>
  </div>
  <a href="tel:+8809639203090" class="hotline">
   <span class="lang-en" style="display:inline">📞 Hotline: 09639-203090</span>
   <span class="lang-bn" style="display:none">📞 হটলাইন: ০৯৬৩৯-২০৩০৯০</span>
  </a>
  <p style="margin-top:20px;font-size:0.88rem;opacity:0.92;max-width:800px;margin-left:auto;margin-right:auto">
   <span class="lang-en" style="display:inline">*After honest pre-screening of weak cases</span>
   <span class="lang-bn" style="display:none">*দুর্বল কেস বাদ দেওয়ার পর</span>
  </p>
 </div>
</section>

<section class="section gold">
 <div class="container">
  <div class="lang-en" style="display:block">
   <h2 class="section-title">Why Choose goFLY Visa Services</h2>
   <div class="cards">
    <div class="card"><h3>⚡ Instant Results</h3><p style="font-weight:500">Get visa categories & processing times in <strong>30 seconds</strong></p></div>
    <div class="card"><h3>🌍 40+ Countries</h3><p style="font-weight:500">Complete coverage - USA, UK, Schengen, Malaysia, Dubai & more</p></div>
    <div class="card"><h3>📊 Real Experience</h3><p style="font-weight:500"><strong>10,000+ real cases</strong> analyzed - not guesswork</p></div>
    <div class="card"><h3>✅ IATA Certified</h3><p style="font-weight:500"><strong>goFLY Limited</strong> - IATA: 42337956</p></div>
    <div class="card"><h3>🛡️ Honest Service</h3><p style="font-weight:500">We decline weak cases to <strong>protect your money & time</strong></p></div>
    <div class="card"><h3>💬 Expert Support</h3><p style="font-weight:500">Direct specialist consultation via WhatsApp</p></div>
   </div>
  </div>
  <div class="lang-bn" style="display:none">
   <h2 class="section-title">কেন goFLY ভিসা সার্ভিস বেছে নেবেন</h2>
   <div class="cards">
    <div class="card"><h3>⚡ তাৎক্ষণিক রেজাল্ট</h3><p style="font-weight:500"><strong>৩০ সেকেন্ডে</strong> ভিসা ক্যাটাগরি ও সময় জানুন</p></div>
    <div class="card"><h3>🌍 ৪০+ দেশ</h3><p style="font-weight:500">সম্পূর্ণ কভারেজ - USA, UK, শেঙ্গেন, মালয়েশিয়া, দুবাই</p></div>
    <div class="card"><h3>📊 বাস্তব অভিজ্ঞতা</h3><p style="font-weight:500"><strong>১০,০০০+ বাস্তব কেস</strong> বিশ্লেষণ করা</p></div>
    <div class="card"><h3>✅ IATA সার্টিফাইড</h3><p style="font-weight:500"><strong>goFLY Limited</strong> - IATA: 42337956</p></div>
    <div class="card"><h3>🛡️ সৎ সার্ভিস</h3><p style="font-weight:500">দুর্বল কেস বাদ দিয়ে <strong>আপনার টাকা-সময় রক্ষা</strong></p></div>
    <div class="card"><h3>💬 বিশেষজ্ঞ সাপোর্ট</h3><p style="font-weight:500">সরাসরি বিশেষজ্ঞের WhatsApp পরামর্শ</p></div>
   </div>
  </div>
 </div>
</section>

<section class="section alt">
 <div class="container">
  <div class="disclaimer-box">
   <div class="lang-en" style="display:block">
    <h3>⚠️ Important Disclaimer</h3>
    <p>This tool provides <strong>indicative guidance</strong> based on general patterns. Individual cases vary. Final visa decision is <strong>100% up to the embassy</strong>. We decline weak cases to protect you. Past success does not guarantee future results.</p>
   </div>
   <div class="lang-bn" style="display:none">
    <h3>⚠️ গুরুত্বপূর্ণ দ্রষ্টব্য</h3>
    <p>এই টুল <strong>সাধারণ প্যাটার্নের উপর ভিত্তি করে নির্দেশমূলক গাইডেন্স</strong> প্রদান করে। ব্যক্তিগত কেস ভিন্ন হতে পারে। চূড়ান্ত সিদ্ধান্ত <strong>১০০% দূতাবাসের</strong>। আমরা দুর্বল কেস বাদ দিই আপনাকে সুরক্ষিত রাখতে।</p>
   </div>
  </div>
 </div>
</section>

<script>
(function(){
 'use strict';

 function switchLang(lang, ev){
  if(ev && ev.target){
   document.querySelectorAll('.lang-btn').forEach(function(b){b.classList.remove('active');});
   ev.target.classList.add('active');
  }
  document.querySelectorAll('.lang-en,.lang-bn').forEach(function(el){
   var isSpan = el.tagName === 'SPAN';
   var isInStats = isSpan && el.parentElement && el.parentElement.classList.contains('trust-stats');
   var shouldShow = el.classList.contains('lang-' + lang);
   el.style.display = shouldShow ? (isSpan || isInStats ? 'inline' : 'block') : 'none';
  });
 }

 document.querySelectorAll('[data-lang]').forEach(function(btn){
  btn.addEventListener('click', function(e){switchLang(btn.getAttribute('data-lang'), e);});
 });

 var progressBar = document.querySelector('.progress-bar');
 var ticking = false;
 function updateProgressBar(){
  var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  var winHeight = document.documentElement.scrollHeight - window.innerHeight;
  var scrollPercent = winHeight > 0 ? Math.min((scrollTop / winHeight) * 100, 100) : 0;
  if(progressBar){progressBar.style.transform = 'translateX(-' + (100 - scrollPercent) + '%)';}
  ticking = false;
 }
 window.addEventListener('scroll', function(){
  if(!ticking){window.requestAnimationFrame(updateProgressBar);ticking = true;}
 }, {passive: true});
})();
</script>

<?php get_footer(); ?>
