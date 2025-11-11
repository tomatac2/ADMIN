import { Button } from "@/components/ui/button";
import businessMeeting from "../assets/business-meeting.jpg";

const CustomerSupport = () => {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Left Content - Office Image with Decorative Shapes */}
          <div className="relative">
            <div className="relative">
              {/* Main Office Image */}
              <img 
                src={businessMeeting} 
                alt="Open-plan office with people working" 
                className="w-full h-auto rounded-2xl shadow-lg"
              />
              
              {/* Decorative Teal Shape - Top Left */}
              <div className="absolute top-4 left-4 w-24 h-16 bg-teal-500 rounded-2xl opacity-80"></div>
              
              {/* Decorative Orange Shape - Bottom Right */}
              <div className="absolute bottom-4 right-4 w-20 h-12 bg-orange-500 rounded-2xl opacity-80"></div>
              
              {/* Decorative Purple Dots - Bottom Left */}
              <div className="absolute bottom-4 left-4 flex gap-2">
                <div className="w-2 h-2 bg-purple-400 rounded-full"></div>
                <div className="w-2 h-2 bg-purple-400 rounded-full"></div>
                <div className="w-2 h-2 bg-purple-400 rounded-full"></div>
                <div className="w-2 h-2 bg-purple-400 rounded-full"></div>
              </div>
            </div>
          </div>

          {/* Right Content - Text and Button */}
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                24/7 Customer Support
              </h2>
              {/* Orange Underline */}
              <div className="w-16 h-1 bg-orange-500 mb-6"></div>
            </div>
            
            <p className="text-lg text-gray-600 leading-relaxed max-w-lg">
              We're Always Here for You! The Driveley support team is ready to assist you anytime. 
              Whether you have a question, issue, or suggestion — we're committed to providing fast, 
              friendly, and reliable help to ensure your experience runs smoothly.
            </p>
            
            <Button 
              className="bg-teal-600 hover:bg-teal-700 text-white px-8 py-5 rounded font-semibold shadow-lg hover:shadow-xl transition-all duration-300"
            >
              Get Support
            </Button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default CustomerSupport;