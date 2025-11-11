import Header from "@/components/Header";
import Footer from "@/components/Footer";
import Statistics from "@/components/Statistics";

const About = () => {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        {/* Hero Section for About */}
        <section className="bg-gradient-hero py-20 lg:py-32">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center space-y-8">
              <h1 className="text-4xl lg:text-6xl font-bold text-foreground">
                About <span className="text-primary">Driverly</span>
              </h1>
              <p className="text-lg text-muted-foreground max-w-3xl mx-auto">
                Driverly is more than just a ride-hailing app; it's your reliable partner for safe, fast, and affordable transportation across Saudi Arabia. Learn about our mission, values, and the story behind creating a seamless experience for both riders and drivers.
              </p>
            </div>
          </div>
        </section>

        <Statistics />
        
        {/* Mission & Vision */}
        <section className="py-20 bg-background">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid lg:grid-cols-2 gap-16">
              <div>
                <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                  Our Mission
                </h2>
                <p className="text-lg text-muted-foreground leading-relaxed mb-8">
                  To revolutionize transportation in Saudi Arabia by providing a safe, reliable, and convenient ride-hailing service that connects riders with professional drivers, making every journey comfortable and affordable.
                </p>
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Safe and secure rides</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Professional drivers</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Affordable pricing</span>
                  </div>
                </div>
              </div>
              
              <div>
                <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                  Our Vision
                </h2>
                <p className="text-lg text-muted-foreground leading-relaxed mb-8">
                  To become the leading mobility platform in the Middle East, creating opportunities for drivers while providing exceptional transportation experiences for our riders.
                </p>
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Innovation in mobility</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Community impact</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-primary rounded-full"></div>
                    <span className="text-foreground">Sustainable growth</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
};

export default About;