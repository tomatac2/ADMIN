const HowItWorks = () => {
  const steps = [
    {
      number: "01",
      title: "Download The App",
      description: "Get started by downloading the Driverly app from App Store or Google Play. Quick setup in under 2 minutes."
    },
    {
      number: "02", 
      title: "Book Your Ride",
      description: "Enter your destination, choose your ride type, and book instantly. Real-time tracking and ETA updates."
    },
    {
      number: "03",
      title: "Enjoy The Ride",
      description: "Meet your professional driver, enjoy a comfortable ride, and pay seamlessly through the app."
    }
  ];

  return (
    <section className="py-20 bg-background">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl lg:text-5xl font-bold text-foreground mb-6">
            How It Works Simple Steps <br /> To Your Destination
          </h2>
          <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
            Getting to your destination has never been easier. Follow these three simple steps and you'll be on your way.
          </p>
        </div>

        <div className="grid md:grid-cols-3 gap-8 lg:gap-12 relative">
          {steps.map((step, index) => (
              <div key={index} className="text-center group relative">
                <div className="relative mb-8 flex items-center justify-center">
                  {/* Circle */}
                  <div className="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors z-10">
                    <span className="text-3xl font-bold text-primary">{step.number}</span>
                  </div>

                  {/* Connector line */}
                  {index < steps.length - 1 && (
                      <div className="hidden md:block absolute top-3/2 left-[230px] w-[340px] h-0.5 bg-border translate-y-[-50%]"></div>
                  )}
                </div>

                <h3 className="text-xl font-semibold text-foreground mb-4">
                  {step.title}
                </h3>

                <p className="text-muted-foreground">
                  {step.description}
                </p>
              </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default HowItWorks;