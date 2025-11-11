import React from "react";
import { Link, usePage } from "@inertiajs/react";

const Header = () => {
  const { url, props } = usePage<any>();
  const header = props.landing_content.header;

  const isActive = (path: string) => {
    return url === path;
  };

  return (
    <header className="w-full bg-background p-2 border-b border-border sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <div className="flex items-center">
            <Link href="/react" className="flex items-center space-x-2">
              <img
                src={header.logo}
                alt="Driverly Logo"
                className="h-8 w-8"
              />
              <span className="text-2xl font-bold text-black">Driverly</span>
            </Link>
          </div>

          {/* Navigation */}
          <nav className="hidden md:flex items-center space-x-8">
            <Link 
              href='/'
              className={`transition-colors ${isActive('/') ? 'font-bold text-black' : 'text-muted-foreground hover:text-black'}`}
            >
              Home
            </Link>
            <Link 
              href='/about'
              className={`transition-colors ${isActive('/about') ? 'font-bold text-black' : 'text-muted-foreground hover:text-black'}`}
            >
              About Driverly
            </Link>
            <Link 
              href='/how-it-works'
              className={`transition-colors ${isActive('/how-it-works') ? 'font-bold text-black' : 'text-muted-foreground hover:text-black'}`}
            >
              How It Works?
            </Link>
            <Link 
              href="/react/become-driver" 
              className={`transition-colors ${isActive('/become-driver') ? 'font-bold text-black' : 'text-muted-foreground hover:text-black'}`}
            >
              Become a Driver
            </Link>
            <Link 
              href='/pricing'
              className={`transition-colors ${isActive('/pricing') ? 'font-bold text-black' : 'text-muted-foreground hover:text-black'}`}
            >
              Pricing
            </Link>
          </nav>

          {/* Language Selector */}
          <div className="flex items-center space-x-4">
            <select name={'locale'} className="bg-transparent text-sm text-muted-foreground rounded px-3 py-1">
              <option value={'en'}>EN</option>
              <option value={'ar'}>AR</option>
            </select>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;