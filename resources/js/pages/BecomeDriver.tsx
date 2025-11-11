import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";

const BecomeDriver = () => {
  const requirements = [
    "Valid Saudi driving license",
    "Vehicle registration documents", 
    "Insurance certificate",
    "Background check clearance",
    "Age 21 or above",
    "Clean driving record"
  ];

  const benefits = [
    {
      title: "Flexible Schedule",
      description: "Drive when you want, earn on your terms"
    },
    {
      title: "Good Earnings",
      description: "Competitive rates and bonus opportunities"
    },
    {
      title: "Support 24/7",
      description: "Our team is always here to help you"
    },
    {
      title: "Easy Setup",
      description: "Get started in just a few simple steps"
    }
  ];

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        {/* Hero Section */}
        <section className="bg-gradient-hero py-20 lg:py-32">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid lg:grid-cols-2 gap-12 items-center">
              <div className="text-center lg:text-left space-y-8">
                <h1 className="text-4xl lg:text-6xl font-bold text-foreground">
                  Become a <span className="text-primary">Driverly</span> Driver
                </h1>
                <p className="text-lg text-muted-foreground max-w-lg mx-auto lg:mx-0">
                  Join thousands of drivers earning good money on their own schedule. Drive with Driverly and be your own boss.
                </p>
                <Button variant="cta" size="lg">
                  Apply Now
                </Button>
              </div>
              
              <div className="relative">
                <div className="bg-card rounded-2xl p-8 shadow-card">
                  <h3 className="text-2xl font-bold text-foreground mb-6">Quick Stats</h3>
                  <div className="space-y-4">
                    <div className="flex justify-between items-center">
                      <span className="text-muted-foreground">Average Weekly Earnings</span>
                      <span className="text-2xl font-bold text-primary">2,500 SAR</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-muted-foreground">Active Drivers</span>
                      <span className="text-2xl font-bold text-primary">50,000+</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-muted-foreground">Cities Available</span>
                      <span className="text-2xl font-bold text-primary">15+</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Benefits */}
        <section className="py-20 bg-background">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                Why Drive with Driverly?
              </h2>
              <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
                Join our community of professional drivers and enjoy the benefits of flexible work
              </p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
              {benefits.map((benefit, index) => (
                <div key={index} className="text-center">
                  <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span className="text-primary text-2xl font-bold">{index + 1}</span>
                  </div>
                  <h3 className="text-xl font-semibold text-foreground mb-3">
                    {benefit.title}
                  </h3>
                  <p className="text-muted-foreground">
                    {benefit.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Requirements */}
        <section className="py-20 bg-muted/30">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid lg:grid-cols-2 gap-12 items-center">
              <div>
                <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                  Driver Requirements
                </h2>
                <p className="text-lg text-muted-foreground mb-8">
                  To ensure the safety and quality of our service, all drivers must meet these requirements:
                </p>
                
                <div className="space-y-4">
                  {requirements.map((requirement, index) => (
                    <div key={index} className="flex items-center gap-3">
                      <div className="w-6 h-6 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <span className="text-white text-sm">✓</span>
                      </div>
                      <span className="text-foreground">{requirement}</span>
                    </div>
                  ))}
                </div>
              </div>
              
              <div className="bg-card rounded-2xl p-8 shadow-card">
                <h3 className="text-2xl font-bold text-foreground mb-6">Ready to Start?</h3>
                <p className="text-muted-foreground mb-6">
                  Complete your application in just a few minutes and start earning with Driverly.
                </p>
                
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <span className="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                    <span className="text-foreground">Submit application</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <span className="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                    <span className="text-foreground">Upload documents</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <span className="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                    <span className="text-foreground">Get approved & start driving</span>
                  </div>
                </div>
                
                <Button variant="cta" size="lg" className="w-full mt-6">
                  Apply to Drive
                </Button>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
};

export default BecomeDriver;