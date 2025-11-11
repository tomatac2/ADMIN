import React from "react";
import { Button } from "./ui/button";
import { Link } from "@inertiajs/react";

const Footer = () => {
  return (
    <footer className="bg-gradient-footer text-foreground">
      {/* Footer Links */}
      <div className="pt-16 pb-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-4 gap-8">
            {/* Company Info */}
            <div className="space-y-4">
              <div className="text-2xl font-bold text-primary">Driverly</div>
              <p className="text-foreground/70 text-sm leading-relaxed">
                Book your ride in seconds and reach your destination safely. 
                Trusted drivers, easy payments, and smooth trips across Saudi Arabia.
                Download now and get moving!
              </p>
            </div>

            {/* Sitemap */}
            <div>
              <h4 className="font-semibold text-foreground mb-4">Sitemap</h4>
              <ul className="space-y-3 text-sm">
                <li><Link href="/react" className="text-foreground/70 hover:text-primary transition-colors">Home</Link></li>
                <li><Link href="/react/about" className="text-foreground/70 hover:text-primary transition-colors">About Driverly</Link></li>
                <li><Link href="/react/how-it-works" className="text-foreground/70 hover:text-primary transition-colors">How It Works?</Link></li>
                <li><Link href="/react/become-driver" className="text-foreground/70 hover:text-primary transition-colors">Become a Driver</Link></li>
              </ul>
            </div>

            {/* Help */}
            <div>
              <h4 className="font-semibold text-foreground mb-4">Help</h4>
              <ul className="space-y-3 text-sm">
                <li><Link href="/react/pricing" className="text-foreground/70 hover:text-primary transition-colors">Pricing</Link></li>
                <li><a href="#" className="text-foreground/70 hover:text-primary transition-colors">Terms & Conditions</a></li>
                <li><a href="#" className="text-foreground/70 hover:text-primary transition-colors">Privacy Policy</a></li>
                <li><a href="#" className="text-foreground/70 hover:text-primary transition-colors">Contact us</a></li>
              </ul>
            </div>

            {/* Newsletter */}
            <div>
              <h4 className="font-semibold text-foreground mb-4">Newsletter</h4>
              <p className="text-foreground/70 text-sm mb-4">
                Enter your email address
              </p>
              <div className="flex gap-2">
                <input 
                  type="email" 
                  placeholder="Enter your email address"
                  className="flex-1 px-3 py-2 text-sm rounded bg-white/60 border border-foreground/10 text-foreground placeholder:text-foreground/50 focus:outline-none focus:ring-2 focus:ring-primary"
                />
                <Button variant="cta" size="sm">
                  Subscribe Now
                </Button>
              </div>
            </div>
          </div>

          {/* Copyright */}
          <div className="border-t border-foreground/10 mt-12 pt-8 text-center">
            <p className="text-foreground/70 text-sm">
              © Copyright 2025. All Rights Reserved by Driverly
            </p>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;