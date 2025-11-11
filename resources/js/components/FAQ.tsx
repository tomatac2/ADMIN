import { useState } from "react";

const FAQ = () => {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const faqs = [
    {
      question: "How do I book a ride with Driverly?",
      answer: "You can easily book a ride by opening the Driverly app, entering your pickup and drop-off locations, choosing your ride type, and confirming your booking. A driver will be assigned to you instantly."
    },
    {
      question: "Can I schedule a ride in advance?",
      answer: "Yes, you can schedule rides up to 7 days in advance through the Driverly app. Simply select your preferred date and time when booking."
    },
    {
      question: "What payment methods are accepted?",
      answer: "We accept various payment methods including credit/debit cards, digital wallets, and cash payments for your convenience."
    },
    {
      question: "How can I contact customer support?",
      answer: "Our 24/7 customer support team is available through the app's help section, phone, email, or live chat. We're always here to assist you."
    },
    {
      question: "Is Driverly available in all cities in Saudi Arabia?",
      answer: "Driverly is currently available in major cities across Saudi Arabia, with plans to expand to more locations soon."
    },
    {
      question: "How do I become a Driverly driver?",
      answer: "To become a Driverly driver, you need to meet our basic requirements, complete the application process, and undergo a background check. Visit our 'Become a Driver' section for more details."
    }
  ];

  return (
    <section className="py-20 bg-muted/30">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
            Frequently Asked Questions
          </h2>
          <p className="text-lg text-muted-foreground">
            Everything you need to know about using Driverly
          </p>
        </div>

        <div className="space-y-4">
          {faqs.map((faq, index) => (
            <div key={index} className="bg-card rounded-xl border border-border overflow-hidden">
              <button
                className="w-full px-6 py-6 text-left flex items-center justify-between hover:bg-muted/30 transition-colors"
                onClick={() => setOpenIndex(openIndex === index ? null : index)}
              >
                <h3 className="font-semibold text-foreground pr-4">
                  {faq.question}
                </h3>
                <div className={`text-primary text-xl transition-transform ${
                  openIndex === index ? 'rotate-45' : ''
                }`}>
                  +
                </div>
              </button>
              
              {openIndex === index && (
                <div className="px-6 pb-6">
                  <p className="text-muted-foreground leading-relaxed">
                    {faq.answer}
                  </p>
                </div>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default FAQ;