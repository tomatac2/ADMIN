import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Header from '@/components/Header';
import Hero from '@/components/Hero';
import HowItWorks from '@/components/HowItWorks';
import GetToKnow from '@/components/GetToKnow';
import Statistics from '@/components/Statistics';
import Testimonials from '@/components/Testimonials';
import FAQ from '@/components/FAQ';
import CustomerSupport from '@/components/CustomerSupport';
import Footer from '@/components/Footer';
import DownloadApp from "../components/downloadApp";

export default function Home(landing_content : any[]) {

    return (
        <AppLayout title="Home">
            <Head title="Drively - Your Trusted Ride-Hailing Service" />
            <Header />
            <Hero />
            <HowItWorks />
            <GetToKnow />
            <Statistics />
            <CustomerSupport />
            <Testimonials />
            <FAQ />
            <DownloadApp />
            <Footer />
        </AppLayout>
    );
}
