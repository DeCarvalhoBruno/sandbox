<?php namespace App\Support\Providers;

class SystemSettings
{
    static $structuredDataOrganizations = [
        'Airline',
        'AnimalShelter',
        'AutomotiveBusiness',
        'ChildCare',
        'Corporation',
        'Dentist',
        'DryCleaningOrLaundry',
        'EducationalOrganization',
        'EmergencyService',
        'EmploymentAgency',
        'EntertainmentBusiness',
        'FinancialService',
        'FoodEstablishment',
        'GovernmentOffice',
        'GovernmentOrganization',
        'HealthAndBeautyBusiness',
        'HomeAndConstructionBusiness',
        'InternetCafe',
        'LegalService',
        'Library',
        'LocalBusiness',
        'LodgingBusiness',
        'MedicalOrganization',
        'NewsMediaOrganization',
        'NGO',
        'Organization',
        'PerformingGroup',
        'ProfessionalService',
        'RadioStation',
        'RealEstateAgent',
        'RecyclingCenter',
        'SelfStorage',
        'ShoppingCenter',
        'SportsActivityLocation',
        'SportsOrganization',
        'Store',
        'TelevisionStation',
        'TouristInformationCenter',
        'TravelAgency'
    ];

    public function makeStructuredData($data)
    {


    }

    public function organizationList(): array
    {
        $orgs = [];
        foreach (self::$structuredDataOrganizations as $org) {
            $orgs[$org] = trans(sprintf('internal.organizations.%s', $org));
        }
        return $orgs;
    }


}