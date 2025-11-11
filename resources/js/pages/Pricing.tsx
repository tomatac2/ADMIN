import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";

const Pricing = () => {
  const rideSizes = [
    {
      name: "Economy",
      description: "Affordable rides for everyday trips",
      basePrice: "5.00",
      perKm: "1.50",
      features: ["4 seats", "Standard vehicle", "Professional driver", "GPS tracking"]
    },
    {
      name: "Comfort", 
      description: "More space and premium vehicles",
      basePrice: "8.00",
      perKm: "2.00",
      features: ["4 seats", "Premium vehicle", "Professional driver", "AC & comfort", "Bottled water"]
    },
    {
      name: "Business",
      description: "Executive rides for business travelers", 
      basePrice: "15.00",
      perKm: "3.00",
      features: ["4 seats", "Luxury vehicle", "Professional driver", "Premium comfort", "Wi-Fi", "Newspapers"]
    }
  ];

  const additionalFees = [
    { service: "Waiting time", price: "0.50 SAR/min" },
    { service: "Airport pickup", price: "10.00 SAR" },
    { service: "Toll roads", price: "As per actual" },
    { service: "Cancellation fee", price: "5.00 SAR" },
    { service: "Peak hours (5-8 PM)", price: "1.5x multiplier" }
  ];

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        {/* Hero Section */}
        <section className="bg-gradient-hero py-20 lg:py-32">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center space-y-8">
              <h1 className="text-4xl lg:text-6xl font-bold text-foreground">
                <span className="text-primary">Driverly</span> Pricing
              </h1>
              <p className="text-lg text-muted-foreground max-w-3xl mx-auto">
                Transparent, affordable pricing with no hidden fees. Choose the ride that fits your budget and comfort needs.
              </p>
            </div>
          </div>
        </section>

        {/* Pricing Cards */}
        <section className="py-20 bg-background">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid md:grid-cols-3 gap-8">
              {rideSizes.map((ride, index) => (
                <div key={index} className={`rounded-2xl p-8 shadow-card ${index === 1 ? 'bg-primary/5 border-2 border-primary' : 'bg-card'}`}>
                  {index === 1 && (
                    <div className="bg-primary text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                      Most Popular
                    </div>
                  )}
                  
                  <h3 className="text-2xl font-bold text-foreground mb-2">{ride.name}</h3>
                  <p className="text-muted-foreground mb-6">{ride.description}</p>
                  
                  <div className="mb-6">
                    <div className="flex items-baseline gap-2 mb-2">
                      <span className="text-3xl font-bold text-primary">{ride.basePrice}</span>
                      <span className="text-muted-foreground">SAR base</span>
                    </div>
                    <div className="flex items-baseline gap-2">
                      <span className="text-xl font-semibold text-foreground">+ {ride.perKm}</span>
                      <span className="text-muted-foreground">SAR per km</span>
                    </div>
                  </div>
                  
                  <ul className="space-y-3 mb-8">
                    {ride.features.map((feature, featureIndex) => (
                      <li key={featureIndex} className="flex items-center gap-3">
                        <div className="w-5 h-5 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                          <span className="text-white text-xs">✓</span>
                        </div>
                        <span className="text-foreground">{feature}</span>
                      </li>
                    ))}
                  </ul>
                  
                  <Button 
                    variant={index === 1 ? "cta" : "hero"} 
                    className="w-full"
                  >
                    Book {ride.name}
                  </Button>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Additional Fees */}
        <section className="py-20 bg-muted/30">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                Additional Fees
              </h2>
              <p className="text-lg text-muted-foreground">
                Complete transparency - here are all possible additional charges
              </p>
            </div>
            
            <div className="bg-card rounded-2xl p-8 shadow-card">
              <div className="space-y-4">
                {additionalFees.map((fee, index) => (
                  <div key={index} className="flex justify-between items-center py-3 border-b border-border last:border-0">
                    <span className="text-foreground font-medium">{fee.service}</span>
                    <span className="text-primary font-semibold">{fee.price}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>

        {/* Price Calculator */}
        <section className="py-20 bg-background">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                Estimate Your Ride Cost
              </h2>
              <p className="text-lg text-muted-foreground">
                Get an instant price estimate for your journey
              </p>
            </div>
            
            <div className="bg-card rounded-2xl p-8 shadow-card">
              <div className="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label className="block text-sm font-medium text-foreground mb-2">From</label>
                  <input 
                    type="text" 
                    placeholder="Enter pickup location"
                    className="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-foreground mb-2">To</label>
                  <input 
                    type="text" 
                    placeholder="Enter destination"
                    className="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  />
                </div>
              </div>
              
              <div className="mb-6">
                <label className="block text-sm font-medium text-foreground mb-2">Ride Type</label>
                <select className="w-full px-4 py-3 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                  <option>Economy</option>
                  <option>Comfort</option>
                  <option>Business</option>
                </select>
              </div>
              
              <Button variant="cta" className="w-full">
                Calculate Price
              </Button>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
};

export default Pricing;