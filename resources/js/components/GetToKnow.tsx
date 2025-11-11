import HowToKnowBackground from "../assets/how_to_know.png";
import Star from "../assets/star.png";

const GetToKnow = () => {

  return (
    <section className="py-10 bg-muted/30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Get to Know Section */}
        <div className="mb-20">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold text-foreground mb-6">
                Get To Know Driverly
              </h2>
              <img
                  src={Star}
                  alt="star"
                  className="shadow-card w-10 h-10"
              />
            </div>
            <div>
              <p className="text-lg text-muted-foreground leading-relaxed">Driveley is a smart ride-hailing app operating in Saudi Arabia, designed to provide a safe, reliable, and fast transportation experience. It combines modern technology with excellent service to connect you with nearby drivers efficiently and effortlessly.</p>
            </div>

          </div>
        </div>
      </div>
      <img
          src={HowToKnowBackground}
          alt="Driverly Team"
          className="shadow-card w-full h-auto"
      />
    </section>
  );
};

export default GetToKnow;