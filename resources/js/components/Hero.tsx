import React from 'react';
import { Button } from "./ui/button";
import phoneMockup1 from "../assets/Mobile Screen 1.png";
import phoneMockup2 from "../assets/Mobile Screen 2.png";
import shapeImage from "../assets/Shape.png";
import appStoreImage from "../assets/app-store.png";
import googlePlayImage from "../assets/google-play.png";

const Hero = () => {
  return (
      <section className="relative min-h-screen flex items-center overflow-hidden bg-white">
        {/* Subtle Background Dots */}
        <div className="absolute top-10 left-20 w-4 h-4 bg-yellow-400 rounded-full"></div>
        <div className="absolute top-10 right-40 w-4 h-4 bg-red-500 rounded-full"></div>
        <div className="absolute top-40 left-[600px] w-3 h-3 bg-blue-500 rounded-full"></div>

        <div className="relative z-10 w-full">
          <div className="max-w-full mx-auto px-4 sm:px-6 lg:px-16">
            <div className="grid lg:grid-cols-4 gap-8 items-center min-h-screen py-20">

              {/* Left Phone Mockup */}
              <div className="hidden md:col-span-1 md:flex justify-center lg:justify-start">
                <div className="relative transform -rotate-12 hover:-rotate-6 transition-transform duration-500">
                  <img
                      src={phoneMockup1}
                      alt="Driverly App Interface"
                      className="w-64 lg:w-80 h-auto rounded-3xl shadow-2xl"
                  />
                </div>
              </div>

              {/* Central Content */}
              <div className="lg:col-span-2 text-center space-y-8">
                <div className="space-y-6">
                  <h1 className="text-gray-800 text-4xl lg:text-6xl font-bold leading-tight">
                  <span className="text-teal-600 relative inline-block">
                    Driverly
                    {/* Shape image underneath */}
                    <img
                        src={shapeImage}
                        alt="Underline decoration"
                        className="absolute -bottom-10 left-0 w-full h-auto"
                    />
                  </span>
                    Your Smart Way to Ride
                  </h1>

                  <p className="text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Discover a seamless, safe, and reliable ride experience with Drively.
                    Whether you're heading to work, exploring the city, or catching a flight,
                    Drively connects you with trusted drivers at the tap of a button.
                  </p>

                  {/* Download the App Text */}
                  <div className="text-teal-600 font-semibold text-lg">
                    Download the App
                  </div>

                  {/* App Store Buttons */}
                  <div className="flex flex-col sm:flex-row gap-4 justify-center">
                    <Button
                        variant="app"
                        size="lg"
                        className="flex items-center gap-3 bg-black text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl"
                    >
                      <div className="flex items-center justify-center">
                        <img src={appStoreImage} alt={'app store'}/>
                      </div>
                      <div className="text-left">
                        <div className="text-xs opacity-80">Download on the</div>
                        <div className="font-semibold text-base">App Store</div>
                      </div>
                    </Button>

                    <Button
                        variant="app"
                        size="lg"
                        className="flex items-center gap-3 bg-black text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl"
                    >
                      <div className="flex items-center justify-center">
                        <img src={googlePlayImage} className={'w-5 h-5'} alt={'google play'}/>
                      </div>
                      <div className="text-left">
                        <div className="text-xs opacity-80">GET IT ON</div>
                        <div className="font-semibold text-base">Google Play</div>
                      </div>
                    </Button>
                  </div>
                </div>
              </div>

              {/* Right Phone Mockup */}
              <div className="hidden md:col-span-1 md:flex justify-center lg:justify-start">
                <div className="relative transform rotate-12 hover:rotate-6 transition-transform duration-500">
                  <img
                      src={phoneMockup2}
                      alt="Driverly Driver Interface"
                      className="w-64 lg:w-80 h-auto rounded-3xl shadow-2xl"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
          <div className="relative flex w-[200%] h-[600px] animate-wave">
            <svg
                className="w-1/2 h-full"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1440 320"
                preserveAspectRatio="none"
            >
              <path
                  fill="#0D9B97"
                  fillOpacity="0.05"
                  d="M0,160L80,154.7C160,149,320,139,480,154.7C640,171,800,213,960,213.3C1120,213,1280,171,1360,149.3L1440,128L1440,320L0,320Z"
              />
            </svg>
            <svg
                className="w-1/2 h-full"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1440 320"
                preserveAspectRatio="none"
            >
              <path
                  fill="#0D9B97"
                  fillOpacity="0.05"
                  d="M0,160L80,154.7C160,149,320,139,480,154.7C640,171,800,213,960,213.3C1120,213,1280,171,1360,149.3L1440,128L1440,320L0,320Z"
              />
            </svg>
          </div>
        </div>
        <div className="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
          <div className="relative flex w-[200%] h-[400px] animate-wave">
            <svg
                className="w-1/2 h-full"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1440 320"
                preserveAspectRatio="none"
            >
              <path
                  fill="#0D9B97"
                  fillOpacity="0.05"
                  d="M0,160L80,154.7C160,149,320,139,480,154.7C640,171,800,213,960,213.3C1120,213,1280,171,1360,149.3L1440,128L1440,320L0,320Z"
              />
            </svg>
            <svg
                className="w-1/2 h-full"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1440 320"
                preserveAspectRatio="none"
            >
              <path
                  fill="#0D9B97"
                  fillOpacity="0.05"
                  d="M0,160L80,154.7C160,149,320,139,480,154.7C640,171,800,213,960,213.3C1120,213,1280,171,1360,149.3L1440,128L1440,320L0,320Z"
              />
            </svg>
          </div>
        </div>

      </section>
  );
};

export default Hero;
