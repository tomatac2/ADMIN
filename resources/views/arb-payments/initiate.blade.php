<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>طلب رحلة - تاكسي دو</title>
  <style>
    :root{
      --orange-600:#0d9b97;
      --orange-500:#0d9b97;
      --orange-100:#fff4ec;
      --muted:#6b6b6b;
      --card-shadow:0 8px 30px rgba(0,0,0,0.08);
      font-family:"Tajawal",system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      background:linear-gradient(180deg,#fff9f4 0%,#fffaf6 40%);
      color:#222;
      padding:28px;
    }
    .container{
      max-width:700px;
      margin:0 auto;
      background:linear-gradient(180deg,var(--orange-100),#fff);
      border-radius:16px;
      padding:24px;
      box-shadow:var(--card-shadow);
    }
    h1{
      margin:0 0 10px 0;
      color:var(--orange-600);
      font-size:22px;
      text-align:center;
    }
    .subtitle{text-align:center;color:var(--muted);margin-bottom:20px}

    label{display:block;margin:8px 0 4px;font-weight:600;color:#333}
    input,select,textarea{
      width:100%;
      padding:10px 12px;
      border-radius:10px;
      border:1px solid hsla(178, 50%, 51%, 0.91);
      background:#fff;
      font-size:15px;
    }

    .row{display:flex;gap:10px}
    .row>div{flex:1}

    .pay-btn{
      width:100%;
      padding:14px;
      border:none;
      border-radius:12px;
      cursor:pointer;
      font-weight:800;
      font-size:16px;
      margin-top:16px;
      background:linear-gradient(90deg,var(--orange-600),var(--orange-500));
      color:#fff;
      box-shadow:0 8px 20px rgba(255,122,24,0.18);
    }
    .pay-btn:active{transform:translateY(1px)}

    .total-box{
      background:#fff8f1;
      border-radius:10px;
      padding:12px;
      margin-top:16px;
      display:flex;
      justify-content:space-between;
      font-weight:700;
      color:var(--orange-600);
    }
    footer{text-align:center;margin-top:20px;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <div class="container">
    <h1>طلب رحلة جديدة</h1>
    <div class="subtitle">أدخل تفاصيل رحلتك ثم اضغط "ادفع" لتأكيد الطلب</div>

    <form id="paymentForm" action="/arb"  method="GET">
      <label>اسم السائق</label>
      <input type="text" name="driver_name" value="أحمد السبيعي" readonly>

      <div class="row">
        <div>
          <label>من (نقطة الانطلاق)</label>
          <input type="text" name="from_address" value="الرياض - شارع التحلية">
        </div>
        <div>
          <label>إلى (الوجهة)</label>
          <input type="text" name="to_address" value="الرياض - شارع العليا">
        </div>
      </div>

      <div class="row">
        <div>
          <label>المسافة التقريبية</label>
          <input type="text" id="distance" name="distance" value="8 كم" readonly>
        </div>
        <div>
          <label>تكلفة الرحلة</label>
          <input type="text" id="fare" name="fare" value="1 ريال" readonly>
        </div>
      </div>

      <label>ملاحظات للسائق (اختياري)</label>
      <textarea name="notes" ></textarea>

      <div class="total-box">
        <div>المجموع الكلي</div>
        <div id="total">1 ريال</div>
      </div>

      <button type="submit" class="pay-btn">ادفع الآن — 1 ريال</button>
    </form>
  </div>

  <footer>© 2025 تاكسي دو — خدمة سيارات الأجرة في الرياض</footer>

   <script>
    (function(){
      var amount = 1;
      document.getElementById('amountField').value = Math.round(amount * 1);
      var form = document.getElementById('paymentForm');
      form.addEventListener('submit', function(e){
        document.getElementById('notesField').value = document.getElementById('notes').value;
        var btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.textContent = 'جارٍ التحويل إلى صفحة الدفع...';
      });
    })();
  </script>
</body>
</html>
