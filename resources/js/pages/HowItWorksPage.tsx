import Header from "@/components/Header";
import Footer from "@/components/Footer";
import HowItWorks from "@/components/HowItWorks";
import FAQ from "@/components/FAQ";

const HowItWorksPage = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        {/* Hero Section */}
        <section className="bg-gradient-hero py-20 lg:py-32">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center space-y-8">
              <h1 className="text-4xl lg:text-6xl font-bold text-foreground">
                How <span className="text-primary">Driverly</span> Works
              </h1>
              <p className="text-lg text-muted-foreground max-w-3xl mx-auto">
                Getting to your destination has never been easier. Follow these simple steps and you'll be on your way in minutes.
              </p>
            </div>
          </div>
        </section>

        <HowItWorks />
        
        {/* Additional Features */}
        <section className="py-20 bg-muted/30">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                Why Choose Driverly?
              </h2>
            </div>
            
            <div className="grid md:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                  <span className="text-2xl">🚗</span>
                </div>
                <h3 className="text-xl font-semibold text-foreground mb-3">Fast Booking</h3>
                <p className="text-muted-foreground">Book your ride in seconds with our intuitive app interface.</p>
              </div>
              
              <div className="text-center">
                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                  <span className="text-2xl">📍</span>
                </div>
                <h3 className="text-xl font-semibold text-foreground mb-3">Real-time Tracking</h3>
                <p className="text-muted-foreground">Track your driver's location and get accurate arrival times.</p>
              </div>
              
              <div className="text-center">
                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                  <span className="text-2xl">💳</span>
                </div>
                <h3 className="text-xl font-semibold text-foreground mb-3">Easy Payment</h3>
                <p className="text-muted-foreground">Multiple payment options including cash, card, and digital wallets.</p>
              </div>
            </div>
          </div>
        </section>

        <FAQ />
      </main>
      <Footer />
    </div>
  );
};

export default HowItWorksPage;