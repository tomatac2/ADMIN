import businessMeeting from "@/assets/business-meeting.jpg";

const Statistics = () => {
  const stats = [
    {
      number: "+500,000",
      label: "Satisfied Rides",
      color: "text-primary"
    },
    {
      number: "+50,000", 
      label: "Registered Drivers",
      color: "text-primary"
    },
    {
      number: "4.8/5",
      label: "User Rating",
      color: "text-primary"
    }
  ];

  return (
    <section className="py-10 bg-muted/30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Statistics */}
        <div className='grid lg:grid-cols-2 gap-12 items-center'>
          <h3 className="text-3xl font-bold text-foreground mb-8 text-center lg:text-left">
            Numbers That Reflect Our Growth and Trust
          </h3>
          
          <div className="grid md:grid-cols-3 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className={`text-3xl lg:text-4xl font-bold ${stat.color} mb-2`}>
                  {stat.number}
                </div>
                <div className="text-muted-foreground text-center font-medium">
                  {stat.label}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Statistics;