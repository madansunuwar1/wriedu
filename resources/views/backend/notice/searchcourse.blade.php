@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
    <style>
    body { font-family: 'Roboto', sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; color: #333; }
    .filter-section { flex: 1 1 200px; max-width: 300px; margin-bottom: 20px; }
    .filter-section h3 { font-size: 16px; margin-bottom: 10px; color: #333; }
    .filter-select { width: 100%; max-width: 100%; padding: 8px; font-size: 14px; border-radius: 4px; border: 1px solid #ddd; }
    .university-logo-container { position: absolute; bottom: -30px; margin-left: 35px; width: 60px; height: 60px; background-color: white; border-radius: 50%; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 0.19); }
    .university-logo { width: 100%; height: 100%; object-fit: contain; }
    .course-stats { font-size: 0.9rem; color: #555; }
    .course-stats div { margin-bottom: 5px; }
    .course-stats i { margin-right: 5px; color: #007bff; }
    @media (max-width: 1200px) { .university-logo-container { right: 200px; } }
    @media (max-width: 992px) { .university-logo-container { right: 150px; } }
    @media (max-width: 768px) { .university-logo-container { right: 100px; } .filter-section { max-width: 100%; } }
    @media (max-width: 576px) { .university-logo-container { right: 50px; } .filter-section { margin-bottom: 10px; } .filter-section h3 { font-size: 14px; } .filter-select { font-size: 12px; } }
</style>

<div class="container-fluid">
    <h1>Search Course</h1>

    @php
        $data_entries = $data_entries ?? collect();
        $universities = $universities ?? collect();
        $uniqueCountries = collect($data_entries)->pluck('country')->filter()->unique()->sort()->values();
        $uniqueLocations = collect($data_entries)->pluck('newLocation')->filter()->unique()->sort()->values();
        $uniqueUniversities = collect($data_entries)->pluck('newUniversity')->filter()->unique()->sort()->values();
        $uniqueCourses = collect($data_entries)->pluck('newCourse')->filter()->unique()->sort()->values();

        $allIndividualIntakes = collect();
        if (isset($data_entries)) {
            foreach ($data_entries as $entry) {
                $intakeString = trim($entry->newIntake ?? '');
                if (!empty($intakeString)) {
                    $individualIntakes = array_filter(array_map('trim', explode(',', $intakeString)), 'strlen');
                    $allIndividualIntakes = $allIndividualIntakes->merge($individualIntakes);
                }
            }
        }
        $uniqueIntakes = $allIndividualIntakes->filter()->unique()->sort()->values();

        // --- MODIFIED/IMPROVED PHP FUNCTION ---
        if (!function_exists('parseGapYearsValue')) {
            function parseGapYearsValue($gapStringInput) {
                if ($gapStringInput === null || trim($gapStringInput) === '') {
                    return 'Infinity'; // Handles null and empty strings
                }

                $trimmedLowerGapString = strtolower(trim($gapStringInput));

                // Prioritize keywords indicating "no limit" or "N/A".
                $infinityKeywords = ['n/a', 'na', 'unlimited', 'no limit', 'any gap'];
                foreach ($infinityKeywords as $keyword) {
                    if (strpos($trimmedLowerGapString, $keyword) !== false) {
                        return 'Infinity';
                    }
                }

                // If the string is exactly a number (e.g., "0", "5")
                if (preg_match('/^(\d+)$/', $trimmedLowerGapString, $matches)) {
                    return (int)$matches[1];
                }

                // If not an exact number, but contains a number (e.g., "up to 5 years")
                if (preg_match('/(\d+)/', $trimmedLowerGapString, $matches)) {
                    return (int)$matches[1];
                }

                // Fallback for any other unparsed strings (e.g., "Varies")
                return 'Infinity';
            }
        }
    @endphp

    <div class="card mb-4">
         <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by Country</h3>
                    <select id="country-select" class="filter-select" multiple placeholder="Countries...">
                        @foreach ($uniqueCountries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by Location</h3>
                    <select id="location-select" class="filter-select" multiple placeholder="Locations...">
                        @foreach ($uniqueLocations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by University</h3>
                    <select id="university-select" class="filter-select" multiple placeholder="Universities...">
                        @foreach ($uniqueUniversities as $university)
                            <option value="{{ $university }}">{{ $university }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by Course</h3>
                    <select id="course-select" class="filter-select" multiple placeholder="Courses...">
                        @foreach ($uniqueCourses as $course)
                            <option value="{{ $course }}">{{ $course }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by Intake</h3>
                    <select id="intake-select" class="filter-select" multiple placeholder="Intakes...">
                        @foreach ($uniqueIntakes as $intake)
                            <option value="{{ $intake }}">{{ $intake }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section">
                    <h3>Filter by Pass Year</h3>
                    <select id="pass-year-select" class="filter-select" placeholder="Select Pass Year...">
                    </select>
                </div>

                <div class="col-md-4 col-lg-2 filter-section d-flex align-items-end">
                    <button id="clear-filters" class="btn btn-secondary w-100">Clear Filters</button>
                </div>
            </div>
        </div>
    </div>


   <div class="entries-container row">
    @forelse ($data_entries as $data_entry)
        @php
            $matchedUniversity = $universities->firstWhere('name', $data_entry->newUniversity);
            $universityImage = $matchedUniversity ? $matchedUniversity->image_link : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPRCMJ0I_ctzjKNjsbnOQF5GKNbR32EsFMig&s';
            $backgroundImage = $matchedUniversity ? $matchedUniversity->background_image : asset('assets/images/backgrounds/bg1.jpg');

            // --- USING THE MODIFIED PHP FUNCTION ---
            $ugGap = parseGapYearsValue($data_entry->newug ?? null);
            $pgGap = parseGapYearsValue($data_entry->newpg ?? null);

            // --- ADDING LEVEL ---
            // ADJUST '$data_entry->level' if your property name is different
            $entryLevel = strtolower($data_entry->level ?? 'both');
        @endphp

        <div class="col-sm-6 col-lg-4 col-xl-3 mb-4 data-entry"
            data-university="{{ $data_entry->newUniversity }}"
            data-course="{{ $data_entry->newCourse }}"
            data-intake="{{ $data_entry->newIntake }}"
            data-country="{{ $data_entry->country }}"
            data-location="{{ $data_entry->newLocation }}"
            data-ug-gap="{{ $ugGap }}"
            data-pg-gap="{{ $pgGap }}"
            data-level="{{ $entryLevel }}"> {{-- NEW: Added level attribute --}}

            <div class="card overflow-hidden hover-img h-100">
                <div class="position-relative">
                     <img src="{{ $backgroundImage }}"
                         class="card-img-top" alt="{{ $data_entry->newUniversity ?? 'N/A' }}" style="height: 150px; object-fit: cover;">

                    <span class="badge text-bg-light fs-2 lh-sm mb-9 me-9 py-1 px-2 fw-semibold position-absolute bottom-0 end-0">
                         {{ $data_entry->newIntake ?? 'N/A' }}
                    </span>

                    <div class="university-logo-container">
                        <img src="{{ $universityImage }}"
                             alt="{{ $data_entry->newUniversity ?? 'N/A' }}"
                             class="img-fluid rounded-circle university-logo"
                             data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $data_entry->newUniversity ?? 'N/A' }}">
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column">
                     <div style="margin-top: 20px;">
                        <span class="badge text-bg-info fs-2 py-1 px-2 lh-sm mt-3 mb-2">{{ $data_entry->country ?? 'N/A' }}</span>

                        <a class="d-block my-2 fs-5 text-dark fw-semibold link-primary" href="{{ route('university.profile', $data_entry->id) }}">
                            {{ $data_entry->newCourse ?? 'N/A Course' }}
                        </a>

                        <div class="fs-6 text-muted mb-3">{{ $data_entry->newUniversity ?? 'N/A University' }}</div>

                        <div class="course-stats mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-map-marker-alt fa-fw"></i>
                                {{ $data_entry->newLocation ?? 'N/A' }}
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-money-bill-wave fa-fw"></i>
                                {{ $data_entry->newAmount ?? 'N/A' }}
                            </div>
                             <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-graduation-cap fa-fw"></i>
                                <span>{{ $data_entry->newScholarship ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('university.profile', $data_entry->id) }}" class="btn btn-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-center text-muted fs-4 mt-5">No course entries found.</p>
        </div>
    @endforelse
</div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const commonTomSelectOptions = {
        plugins: ['remove_button'],
        create: false,
        sortField: { field: 'text', direction: 'asc' }
    };

    const countrySelect = new TomSelect('#country-select', commonTomSelectOptions);
    const locationSelect = new TomSelect('#location-select', commonTomSelectOptions);
    const universitySelect = new TomSelect('#university-select', commonTomSelectOptions);
    const courseSelect = new TomSelect('#course-select', commonTomSelectOptions);
    const intakeSelect = new TomSelect('#intake-select', commonTomSelectOptions);
    const passYearSelect = new TomSelect('#pass-year-select', {
        create: false,
        sortField: { field: 'value', direction: 'desc' },
        placeholder: 'Select Pass Year...',
        allowEmptyOption: true
    });

    const currentYear = new Date().getFullYear();
    for (let i = 0; i < 20; i++) {
        const year = currentYear - i;
        passYearSelect.addOption({ value: year.toString(), text: year.toString() });
    }
    passYearSelect.refreshOptions(false);


    const allEntries = [];
    document.querySelectorAll('.data-entry').forEach(entryElement => { // Renamed for clarity
        allEntries.push({
            element: entryElement,
            country: entryElement.getAttribute('data-country'),
            location: entryElement.getAttribute('data-location'),
            university: entryElement.getAttribute('data-university'),
            course: entryElement.getAttribute('data-course'),
            intake: entryElement.getAttribute('data-intake'),
            ugGap: parseGapAttribute(entryElement.getAttribute('data-ug-gap')),
            pgGap: parseGapAttribute(entryElement.getAttribute('data-pg-gap')),
            level: entryElement.getAttribute('data-level') // --- MODIFIED: Reading level ---
        });
    });

    function parseGapAttribute(gapAttr) {
        if (gapAttr === null || gapAttr === '') {
            return Infinity;
        }
        // Check for string "Infinity" or "unlimited" which comes from PHP
        if (String(gapAttr).toLowerCase() === 'infinity' || String(gapAttr).toLowerCase() === 'unlimited') {
            return Infinity;
        }
        const num = parseInt(gapAttr, 10);
        // If parsing fails (e.g., for non-numeric strings not caught as "Infinity" above), treat as Infinity
        return isNaN(num) ? Infinity : num;
    }


    function getUniqueValues(entries, attribute) {
        return [...new Set(entries.map(entry => entry[attribute]).filter(val => val != null))]
                 .sort();
    }

    function collectAndSplitIntakes(entries) {
         let allIndividualIntakes = [];
         entries.forEach(entry => {
             const intakeString = entry.intake || '';
             const individualIntakes = intakeString.split(',')
                                               .map(s => s.trim())
                                               .filter(s => s.length > 0);
             allIndividualIntakes = allIndividualIntakes.concat(individualIntakes);
         });
         return [...new Set(allIndividualIntakes)].sort();
    }

     function performSearch() {
        const selectedCountries = countrySelect.items;
        const selectedLocations = locationSelect.items;
        const selectedUniversities = universitySelect.items;
        const selectedCourses = courseSelect.items;
        const selectedIntakes = intakeSelect.items;
        const selectedPassYear = passYearSelect.getValue();

        let visibleEntries = allEntries.filter(entry => { // 'entry' is an object from allEntries
            const countryMatch = selectedCountries.length === 0 || selectedCountries.includes(entry.country);
            const locationMatch = selectedLocations.length === 0 || selectedLocations.includes(entry.location);
            const universityMatch = selectedUniversities.length === 0 || selectedUniversities.includes(entry.university);
            const courseMatch = selectedCourses.length === 0 || selectedCourses.includes(entry.course);

            const entryIntakeString = entry.intake || '';
            const entryIntakesArray = entryIntakeString.split(',')
                                                  .map(s => s.trim())
                                                  .filter(s => s.length > 0);
            const intakeMatch = selectedIntakes.length === 0 || selectedIntakes.some(selIntake => entryIntakesArray.includes(selIntake));

            // --- MODIFIED: Pass Year Match Logic based on Level ---
            let passYearMatch = true;
            if (selectedPassYear && selectedPassYear !== '') {
                const passYearNum = parseInt(selectedPassYear, 10);
                const currentAcademicYear = new Date().getFullYear();
                const gap = currentAcademicYear - passYearNum;

                if (gap < 0) { // Graduated in the future
                    passYearMatch = false;
                } else {
                    const ugAcceptable = (gap <= entry.ugGap);
                    const pgAcceptable = (gap <= entry.pgGap);

                    // entry.level is "undergraduate", "postgraduate", or "both" (or default)
                    switch (entry.level) {
                        case 'undergraduate':
                            passYearMatch = ugAcceptable;
                            break;
                        case 'postgraduate':
                            passYearMatch = pgAcceptable;
                            break;
                        case 'both':
                        default: // Handles 'both', null, or any unexpected level values
                            passYearMatch = ugAcceptable || pgAcceptable;
                            break;
                    }
                }
            }
            // --- END MODIFIED Pass Year Match Logic ---

            const isVisible = countryMatch && locationMatch && universityMatch && courseMatch && intakeMatch && passYearMatch;
            entry.element.style.display = isVisible ? '' : 'none';
            return isVisible;
        });

        updateFilterOptions(visibleEntries);
    }


    function updateFilterOptions(visibleEntries) {
        countrySelect.off('change');
        locationSelect.off('change');
        universitySelect.off('change');
        courseSelect.off('change');
        intakeSelect.off('change');

        const selectedCountries = new Set(countrySelect.items);
        const selectedLocations = new Set(locationSelect.items);
        const selectedUniversities = new Set(universitySelect.items);
        const selectedCourses = new Set(courseSelect.items);
        const selectedIntakes = new Set(intakeSelect.items);

        // Filter for available locations based on selected countries (or all if none selected)
        const entriesForLocation = visibleEntries.filter(entry => selectedCountries.size === 0 || selectedCountries.has(entry.country));
        const availableLocations = getUniqueValues(entriesForLocation, 'location');
        updateSelectOptions(locationSelect, availableLocations, selectedLocations);

        // Filter for available universities based on selected countries AND locations
        const entriesForUniversity = entriesForLocation.filter(entry => selectedLocations.size === 0 || selectedLocations.has(entry.location));
        const availableUniversities = getUniqueValues(entriesForUniversity, 'university');
        updateSelectOptions(universitySelect, availableUniversities, selectedUniversities);

        // Filter for available courses based on selected countries, locations, AND universities
        const entriesFilteredByUniversity = entriesForUniversity.filter(entry => selectedUniversities.size === 0 || selectedUniversities.has(entry.university));
        const availableCourses = getUniqueValues(entriesFilteredByUniversity, 'course');
        updateSelectOptions(courseSelect, availableCourses, selectedCourses);

        // Filter for available intakes based on selected countries, locations, universities AND courses
        const entriesFilteredByCourse = entriesFilteredByUniversity.filter(entry => selectedCourses.size === 0 || selectedCourses.has(entry.course));
        const availableIntakes = collectAndSplitIntakes(entriesFilteredByCourse);
        updateSelectOptions(intakeSelect, availableIntakes, selectedIntakes);

        // Re-attach event listeners
        countrySelect.on('change', performSearch);
        locationSelect.on('change', performSearch);
        universitySelect.on('change', performSearch);
        courseSelect.on('change', performSearch);
        intakeSelect.on('change', performSearch);
    }

    function updateSelectOptions(selectInstance, availableOptions, selectedOptionsSet) {
        const currentOptions = Object.keys(selectInstance.options);
        // Remove options that are no longer available and not selected
        currentOptions.forEach(optionValue => {
            if (!availableOptions.includes(optionValue) && !selectedOptionsSet.has(optionValue)) {
                selectInstance.removeOption(optionValue);
            }
        });
        // Add new available options that are not already present
        availableOptions.forEach(optionValue => {
            if (optionValue && !selectInstance.options.hasOwnProperty(optionValue)) {
                selectInstance.addOption({value: optionValue, text: optionValue});
            }
        });
        selectInstance.refreshOptions(false); // false to prevent triggering 'change' event
    }


    function restoreAllOptions(selectInstance, allPossibleOptions) {
        const currentSelected = new Set(selectInstance.items); // Preserve current selections

         // Clear existing options except those that are selected
         Object.keys(selectInstance.options).forEach(optionKey => {
             if (!currentSelected.has(optionKey)) {
                 selectInstance.removeOption(optionKey, true); // true to suppress events
             }
         });

        // Add all possible options
        allPossibleOptions.forEach(option => {
             if (option && !selectInstance.options.hasOwnProperty(option)) { // Add if not already an option
                selectInstance.addOption({value: option, text: option}, true); // true to suppress events
             }
         });

         // Ensure selected items are definitely present as options (in case they were filtered out)
         currentSelected.forEach(item => {
             if (item && !selectInstance.options.hasOwnProperty(item)) {
                 selectInstance.addOption({value: item, text: item}, true);
             }
         });

        selectInstance.refreshOptions(false); // Refresh display, false to suppress events
    }


    document.getElementById('clear-filters').addEventListener('click', function() {
        countrySelect.clear();
        locationSelect.clear();
        universitySelect.clear();
        courseSelect.clear();
        intakeSelect.clear();
        passYearSelect.clear(); // Clears the selected value for pass year

        // Make all entries visible again
        allEntries.forEach(entry => {
            entry.element.style.display = '';
        });

        // Restore all dropdown options to their initial full set
        restoreAllOptions(countrySelect, getUniqueValues(allEntries, 'country'));
        restoreAllOptions(locationSelect, getUniqueValues(allEntries, 'location'));
        restoreAllOptions(universitySelect, getUniqueValues(allEntries, 'university'));
        restoreAllOptions(courseSelect, getUniqueValues(allEntries, 'course'));
        restoreAllOptions(intakeSelect, collectAndSplitIntakes(allEntries));
        // No need to restore passYearSelect options as they are static years
    });

    // Initial setup of event listeners
    countrySelect.on('change', performSearch);
    locationSelect.on('change', performSearch);
    universitySelect.on('change', performSearch);
    courseSelect.on('change', performSearch);
    intakeSelect.on('change', performSearch);
    passYearSelect.on('change', performSearch);

    // Initialize tooltips if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initial filter application to set correct dependent dropdowns
    performSearch();
});
</script>
@endsection