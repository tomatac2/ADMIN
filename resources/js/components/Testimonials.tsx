import React, { useState } from 'react';

const Testimonials = () => {
  const testimonials = [
    {
      name: "Cameron Williamson",
      source: "App Store",
      profile: "👨‍💼", // Placeholder for profile image
      text: "I've been using Drivele for the past month, and it's quickly become my go-ride app. The interface is smooth, bookings are fast, and drivers are always polite and professional. I especially love how accurate the estimated time of arrival is. The app a..."
    },
    {
      name: "Brooklyn Simmons",
      source: "Google Play", 
      profile: "👨‍💼", // Placeholder for profile image
      text: "Drivele has made my daily commute so much easier. The app is well-designed, user-friendly, and quick to load. I really appreciate the multiple payment options, including STC Pay and Mada. The drivers I've had so far have all been on time and respectful. I also had a question once and the s..."
    },
    {
      name: "Marvin McKinney",
      source: "Google Play",
      profile: "👨‍💼", // Placeholder for profile image
      text: "One of the best ride-hailing apps I've tried in the region! Drivele stands out because of its focus on safety and simplicity. As a female passenger, I feel secure knowing the app verifies all drivers and tracks my location. The scheduled ride option is a game changer for airport runs or importan..."
    }
  ];

  const [currentIndex, setCurrentIndex] = useState(0);

  const nextSlide = () => {
    setCurrentIndex((prevIndex) => 
      prevIndex === testimonials.length - 1 ? 0 : prevIndex + 1
    );
  };

  const prevSlide = () => {
    setCurrentIndex((prevIndex) => 
      prevIndex === 0 ? testimonials.length - 1 : prevIndex - 1
    );
  };

  // Get the current set of testimonials to display
  const getVisibleTestimonials = () => {
    const visible = [];
    for (let i = 0; i < 3; i++) {
      const index = (currentIndex + i) % testimonials.length;
      visible.push(testimonials[index]);
    }
    return visible;
  };

  return (
    <section className="py-20 bg-gray-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          {/* Testimonials Label */}
          <div className="inline-block bg-gray-700 text-gray-300 text-sm px-4 py-2 rounded-full mb-4">
            Testimonials
          </div>
          
          {/* Main Heading */}
          <h2 className="text-3xl lg:text-4xl font-bold text-white mb-6">
            Customer Reviews
          </h2>
        </div>

        {/* Testimonials Carousel */}
        <div className="relative">
          {/* Navigation Buttons */}
          <button 
            onClick={prevSlide}
            className="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow duration-300"
            aria-label="Previous testimonial"
          >
            <svg className="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <button 
            onClick={nextSlide}
            className="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow duration-300"
            aria-label="Next testimonial"
          >
            <svg className="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
          </button>

          {/* Testimonials Grid */}
          <div className="grid md:grid-cols-3 gap-8 px-16">
            {getVisibleTestimonials().map((testimonial, index) => (
              <div key={`${currentIndex}-${index}`} className="bg-gray-700 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                {/* Profile Section */}
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-16 h-16 bg-gray-600 rounded-full flex items-center justify-center text-2xl">
                    {testimonial.profile}
                  </div>
                  <div>
                    <h4 className="font-semibold text-white text-lg">
                      {testimonial.name}
                    </h4>
                    <p className="text-gray-400 text-sm">
                      {testimonial.source}
                    </p>
                  </div>
                </div>
                
                {/* Review Text */}
                <p className="text-white leading-relaxed">
                  "{testimonial.text}"
                </p>
              </div>
            ))}
          </div>

          {/* Dots Indicator */}
          <div className="flex justify-center mt-8 space-x-2">
            {testimonials.map((_, index) => (
              <button
                key={index}
                onClick={() => setCurrentIndex(index)}
                className={`w-3 h-3 rounded-full transition-all duration-300 ${
                  index === currentIndex ? 'bg-white scale-125' : 'bg-gray-500 hover:bg-gray-400'
                }`}
                aria-label={`Go to testimonial ${index + 1}`}
              />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Testimonials;