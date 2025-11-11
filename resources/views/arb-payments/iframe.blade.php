<script>
document.addEventListener("DOMContentLoaded", function () {
    // رابط الدفع
    var paymentUrl = "https://securepayments.alrajhibank.com.sa/pg/paymentpage.htm?PaymentID=600202530696691747";
    // إنشاء الـ iframe داخل صفحة الدفع
    function showPaymentFrame() {
        // تأكد إن مفيش إطار مفتوح بالفعل
        if (document.getElementById("payframe")) return;

        var division = document.createElement("div");
        division.id = "payframe";
        division.style = `
            min-height: 100%;
            transition: all 0.3s ease-out;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
        `;

        division.innerHTML = `
            <div id="F" class="close" style="
                position: absolute;
                right: 10px;
                top: 10px;
                cursor: pointer;
                font-size: 24px;
                opacity: .8;
                width: 24px;
                text-align: center;
                line-height: 24px;
                background: #fff;
                border-radius: 50%;
                z-index: 10000;
            " onclick="
                document.getElementById('iframe').remove();
                document.getElementById('payframe').remove();
            ">×</div>
            <iframe id="iframe"
                style="
                    opacity: 1;
                    height: 100%;
                    width: 65%;
                    margin-left: 17.5%;
                    border: 0;
                    display: block;
                    background: none;
                    z-index: 2;
                "
                allowtransparency="true"
                frameborder="0"
                allowpaymentrequest="true"
                src="` + paymentUrl + `">
            </iframe>
        `;

        document.body.appendChild(division);
    }

    // افتح iframe مباشرة (أو يمكنك ربطها بزر لاحقًا)
    showPaymentFrame();
});
</script>

