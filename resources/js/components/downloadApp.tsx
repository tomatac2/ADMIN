import {Button} from "./ui/button";
import React from "react";
import appStoreImage from "../assets/app-store.png";
import googlePlayImage from "../assets/google-play.png";

const DownloadApp = () => {

  return (
    <section className="p-10 bg-muted/30">
      <div className="bg-gradient-download rounded-3xl py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold text-[#141414] mb-6">
            Download the Driverly App
            Your Ride, Your Way!
          </h2>

          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <div className="flex items-center gap-2 text-[#141414]">
              <span className="text-xl text-green-500">✓</span>
              <span>Easy & fast booking</span>
            </div>
            <div className="flex items-center gap-2 text-[#141414]">
              <span className="text-xl text-green-500">✓</span>
              <span>Safe & professional drivers</span>
            </div>
            <div className="flex items-center gap-2 text-[#141414]">
              <span className="text-xl text-green-500">✓</span>
              <span>Multiple payment options</span>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button
                variant="app"
                size="lg"
                className="flex items-center gap-3 bg-black text-white px-8 py-4 rounded-xl"
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
                className="flex items-center gap-3 bg-black text-white px-8 py-4 rounded-xl"
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
    </section>
  );
};

export default DownloadApp;