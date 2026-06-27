<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class HeatResistantPaintKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(['name' => 'Engineering']);

        $entries = [

            [
                'title'   => 'Introduction to Heat-Resistant Paints: Types, Binders, and Temperature Ranges',
                'summary' => 'A foundational overview of heat-resistant paint chemistry, binder systems, and the temperature ratings that govern product selection.',
                'tags'    => ['heat-resistant paint', 'high-temperature coating', 'binder systems', 'silicone', 'epoxy'],
                'content' => <<<MD
# Introduction to Heat-Resistant Paints: Types, Binders, and Temperature Ranges

Heat-resistant paints (also called high-temperature coatings) are specialty formulations designed to maintain adhesion, colour, and protective properties when exposed to sustained or cyclic heat. Unlike conventional decorative or industrial paints, their binder systems are engineered to withstand oxidation, thermal degradation, and dimensional changes of the substrate.

## Primary Binder Systems

| Binder Type | Continuous Service Temp | Key Advantage |
|---|---|---|
| Silicone alkyd | Up to 200 °C | Good colour retention, low cost |
| Pure silicone resin | 400 – 600 °C | Excellent heat stability |
| Modified silicone + ceramic filler | 600 – 800 °C | Enhanced insulative properties |
| Inorganic zinc silicate | Up to 400 °C | Cathodic protection of steel |
| Phosphate-bonded inorganic | 800 – 1 200 °C | Suitable for furnace components |

## How Temperature Ratings Are Determined

Manufacturers test coatings under **ISO 12944-9** (cyclic corrosion) and proprietary furnace-ageing protocols. A coating rated at 600 °C means it survives continuous exposure without blistering, chalking, or peeling — short excursions above the limit are sometimes permissible.

## Selection Criteria

1. **Substrate type** — carbon steel, stainless steel, aluminium, or cast iron each have different thermal expansion coefficients.
2. **Exposure profile** — continuous vs. intermittent heat has a significant effect on binder choice.
3. **Corrosion environment** — marine, chemical, or atmospheric service may demand additional corrosion inhibitors.
4. **VOC regulations** — water-borne silicone formulations are increasingly mandated in regions with strict air-quality codes.

## Application Notes

Most heat-resistant paints require a **burn-off or curing cycle** at elevated temperature before they reach full performance. This thermally cross-links the silicone network and drives off residual solvent. Skipping this step is the most common cause of early coating failure.
MD,
            ],

            [
                'title'   => 'Silicone Resin Coatings: Chemistry, Cross-Linking, and High-Temperature Performance',
                'summary' => 'A deep dive into the polyorganosiloxane chemistry that makes silicone the benchmark binder for coatings above 300 °C.',
                'tags'    => ['silicone resin', 'polyorganosiloxane', 'cross-linking', 'high-temperature', 'coating chemistry'],
                'content' => <<<MD
# Silicone Resin Coatings: Chemistry, Cross-Linking, and High-Temperature Performance

Silicone (polyorganosiloxane) resins are the dominant binder for heat-resistant coatings above 300 °C. Their silicon–oxygen backbone (Si–O–Si) has bond energy of ~450 kJ/mol, far exceeding the C–C bonds in conventional organic resins (~350 kJ/mol), which explains their exceptional thermal stability.

## Molecular Architecture

Silicone resins are produced from chlorosilane monomers by hydrolysis and condensation. The degree of cross-linking — controlled by the ratio of tri- and tetra-functional silanes — determines hardness and heat resistance:

- **Low cross-link density** → flexible, good adhesion, limited to ~400 °C
- **High cross-link density** → harder film, up to 600 °C continuous service

## Methyl vs. Phenyl Silicone

| Property | Methyl silicone | Phenyl silicone |
|---|---|---|
| Max service temp | ~500 °C | ~600 °C |
| Flexibility | Good | Lower |
| Water repellency | Excellent | Moderate |
| Cost | Lower | Higher |

Phenyl groups increase thermal stability because the aromatic ring requires more energy to decompose.

## Cure Mechanism

Silicone coatings are typically supplied as:
- **One-component** (moisture or heat cure)
- **Two-component** (amine or tin-catalyst cross-linker)

Industrial high-temperature grades are heat-cured at 200–250 °C in an oven or in-situ on the first operational heat-up.

## Recent Developments (2024–2025)

- **Nano-silica reinforcement**: Adding 3–5 wt % fumed silica improves film hardness and scratch resistance without reducing flexibility.
- **Hybrid silicone-epoxy novolac**: Provides chemical resistance at ambient and heat resistance at elevated temperatures, useful for petrochemical vessels.
- **Waterborne silicone dispersions**: VOC < 50 g/L versions now meet EU Directive 2004/42/EC and US EPA Method 24 requirements.
MD,
            ],

            [
                'title'   => 'Ceramic-Filled Heat-Resistant Coatings: Fillers, Formulation, and Thermal Insulation',
                'summary' => 'How ceramic micro- and nano-fillers extend temperature ratings and add insulative function to high-temperature coatings.',
                'tags'    => ['ceramic filler', 'thermal insulation', 'hollow microspheres', 'alumina', 'heat-resistant coating'],
                'content' => <<<MD
# Ceramic-Filled Heat-Resistant Coatings: Fillers, Formulation, and Thermal Insulation

Adding ceramic fillers to silicone or inorganic binders can push temperature resistance to 800–1 200 °C and introduce genuine thermal-barrier functionality, reducing heat transfer through the coated substrate.

## Common Ceramic Fillers

| Filler | Function | Typical Loading |
|---|---|---|
| Aluminium oxide (Al₂O₃) | Heat resistance, hardness | 10–30 wt % |
| Silicon carbide (SiC) | Thermal conductivity, hardness | 5–15 wt % |
| Hollow glass/ceramic microspheres | Thermal insulation (low λ) | 15–40 vol % |
| Talc | Barrier, anti-sag | 5–10 wt % |
| Zinc dust | Cathodic protection | 20–70 wt % |

## Thermal Insulation Mechanism

Hollow ceramic microspheres (HCM) are the key additive for insulative grades. Their gas-filled void reduces the coating's thermal conductivity to 0.05–0.20 W/(m·K), vs. 0.5–1.0 W/(m·K) for unfilled silicone. This allows surface temperature reductions of 20–60 °C in industrial pipe-insulation applications.

## Formulation Challenges

- **Pigment volume concentration (PVC)** must be balanced: too many fillers reduce film cohesion.
- **Particle size distribution** affects application — microspheres > 100 µm may block airless spray tips.
- **Settling** of dense alumina requires thixotropic additives (e.g., organoclay, fumed silica).

## Application: Furnace and Oven Linings

Ceramic-filled phosphate-bonded coatings (service to 1 200 °C) are brush-applied to furnace brickwork and steel shells in steel mills and ceramic kilns. They reduce radiative heat loss and protect refractory surfaces from chemical attack.

## 2025 Trend: Aerogel-Silicone Hybrid Coatings

Silica aerogel particles (λ ≈ 0.015 W/(m·K)) blended into silicone matrices are under active development. Pilot projects in LNG facility piping have shown 40 % better insulative performance compared to conventional HCM-filled systems, at comparable cost.
MD,
            ],

            [
                'title'   => 'Surface Preparation for Heat-Resistant Coatings: Standards, Methods, and Common Failures',
                'summary' => 'Why surface preparation is the single most critical factor in heat-resistant coating performance and how to meet international standards.',
                'tags'    => ['surface preparation', 'blast cleaning', 'ISO 8501', 'SSPC', 'coating adhesion'],
                'content' => <<<MD
# Surface Preparation for Heat-Resistant Coatings: Standards, Methods, and Common Failures

Industry data consistently shows that **60–80 % of coating failures** are attributable to inadequate surface preparation rather than product quality. This is especially true for heat-resistant coatings, where thermal cycling amplifies any adhesion weakness.

## International Standards

| Standard | Scope |
|---|---|
| ISO 8501-1 | Visual cleanliness grades (Sa 1, Sa 2, Sa 2½, Sa 3) |
| ISO 8502-3 | Dust contamination assessment (tape test) |
| ISO 8502-6 | Soluble salt extraction (Bresle method) |
| SSPC-SP 6 / NACE 3 | Commercial blast (equivalent to Sa 2) |
| SSPC-SP 10 / NACE 2 | Near-white blast (equivalent to Sa 2½) |

Most heat-resistant paint datasheets specify a minimum of **Sa 2½** (near-white metal blast) for service above 300 °C.

## Surface Profile

Anchor profile (surface roughness) should match the coating DFT (dry film thickness):
- **Rz 40–70 µm** for single-coat applications at 40–80 µm DFT
- **Rz 60–100 µm** for multi-coat systems

Excessive profile peaks (Rz > 120 µm) cause thin spots over peaks, leading to early corrosion initiation.

## Contamination Limits

| Contaminant | Limit |
|---|---|
| Soluble salts (chloride) | < 20 mg/m² (offshore) / < 50 mg/m² (general industry) |
| Oil/grease | Zero — solvent or detergent wash required |
| Dust rating | Rating 1 or 2 per ISO 8502-3 |

## Common Failures from Poor Preparation

1. **Chloride-induced under-film corrosion** — osmotic blistering at first heat-up
2. **Loss of adhesion over mill scale** — mill scale separates from substrate during thermal cycling
3. **Delamination at weld seams** — slag inclusions and weld spatter not removed
4. **Solvent contamination** — residual cleaning solvent trapped under coating vapourises, causing pinholes

## Practical Checklist

- [ ] Blast to Sa 2½ minimum
- [ ] Check profile with replica tape (Testex Press-O-Film)
- [ ] Bresle test for chlorides
- [ ] Apply coating within 4 hours of blasting (or before visible rust-back)
- [ ] Verify ambient conditions: steel temp > 3 °C above dew point
MD,
            ],

            [
                'title'   => 'Heat-Resistant Paint for Exhaust Systems: Automotive and Industrial Standards (2024–2025)',
                'summary' => 'Performance requirements, test protocols, and product developments for coatings on exhaust manifolds, stacks, and silencers.',
                'tags'    => ['exhaust coating', 'automotive heat paint', 'exhaust manifold', 'thermal cycling', 'VDA 232-102'],
                'content' => <<<MD
# Heat-Resistant Paint for Exhaust Systems: Automotive and Industrial Standards (2024–2025)

Exhaust systems present one of the harshest environments for any coating: rapid thermal cycling from –40 °C to over 700 °C, combined with road salts, water immersion, stone impact, and vibration. Automotive OEMs now apply coatings at the factory in addition to the aftermarket segment.

## Temperature Zones in Automotive Exhaust

| Component | Peak Metal Temperature |
|---|---|
| Exhaust manifold (gasoline) | 650 – 900 °C |
| Turbocharger housing | 700 – 950 °C |
| Catalytic converter housing | 500 – 700 °C |
| Mid-pipe / centre silencer | 300 – 500 °C |
| Rear silencer | 150 – 300 °C |

## Applicable Standards

- **VDA 232-102**: German automotive cyclic corrosion test, includes high-temperature excursions
- **SAE J2671**: Test procedure for exhaust system heat shields
- **ISO 16750-4**: Environmental testing of road vehicle electrical equipment (thermal)

## Coating Types by Zone

- **Manifold / turbo (> 600 °C)**: Phosphate-bonded or high-phenyl silicone, typically applied by dip-spin or electrostatic spray, 20–40 µm DFT.
- **Mid-pipe (300–600 °C)**: Pure silicone or modified silicone, often pigmented with aluminium flake for reflectivity and corrosion barrier.
- **Rear silencer (< 300 °C)**: Silicone-alkyd or even modified polyester for cost efficiency.

## 2024–2025 Industry Developments

- **EV thermal management**: Battery housings and power electronics use modified silicone coatings for thermal interface and arc-flash resistance, driven by the EV transition.
- **Biobased silicone feedstocks**: Wacker and Shin-Etsu both announced partial bio-sourced silicone resin lines in 2024 to meet Scope 3 emission targets.
- **Powder-coat heat-resistant grades**: New powder coatings rated to 650 °C (PPG, AkzoNobel) reduce VOC to near-zero and are being adopted by exhaust manufacturers in the EU following tightening of solvent regulations.

## Aftermarket Products: What to Look for

Aerosol heat-resistant sprays (e.g., VHT Flameproof, Rust-Oleum High Heat) typically use silicone resins and are rated 315–650 °C. Key purchase criteria:

1. Stated continuous-service temperature rating
2. Whether cure cycle is needed (most aerosols require 250 °C bake)
3. VOC content if applying indoors
MD,
            ],

            [
                'title'   => 'Intumescent vs. Heat-Resistant Coatings: Differences, Selection Guide, and Combined Systems',
                'summary' => 'Clarifying the fundamental difference between fire-protection intumescent coatings and true heat-resistant paints, with a selection decision tree.',
                'tags'    => ['intumescent coating', 'passive fire protection', 'heat-resistant paint', 'structural steel', 'selection guide'],
                'content' => <<<MD
# Intumescent vs. Heat-Resistant Coatings: Differences, Selection Guide, and Combined Systems

These two coating types are frequently confused, yet they serve fundamentally different purposes. Using the wrong type can result in unsafe structures or wasted cost.

## Core Difference

| Property | Intumescent Coating | Heat-Resistant Paint |
|---|---|---|
| Primary function | Insulate steel from fire for a defined time (30–120 min) | Withstand continuous operational heat without degrading |
| Mechanism | Expands 10–100× in volume when heated, forming char | Remains chemically stable; no volume change |
| Operational temperature | Ambient only — not for continuous heat | 200 – 1 200 °C continuous service |
| Standards | EN 13501-2, UL 263 (fire test) | ISO 12944, ASTM D2485 (heat ageing) |
| Typical DFT | 500 – 5 000 µm (thick builds) | 25 – 150 µm |

## When to Use Which

**Use intumescent coatings when:**
- The structural steel is at ambient temperature in normal operation
- You need a fire rating (e.g., R30, R60, R120 per EN 1363-1)
- The risk is accidental fire, not operational heat

**Use heat-resistant paint when:**
- The surface routinely reaches > 200 °C in normal operation (boilers, ovens, stacks, engines)
- You need corrosion protection at elevated temperature
- The surface is a flue, duct, pipe, or industrial equipment

## Combined Systems

Some engineering scenarios require both:
- **Hot columns at risk of fire**: A heat-resistant primer is applied directly to the steel, followed by an intumescent topcoat that provides the fire rating.
- **Fire-exposed process vessels**: Thin-film intumescent on vessel heads, heat-resistant coating on the body which is in continuous contact with hot process fluids.

## Key Warning

Applying intumescent coating to a hot operating surface will cause it to expand in normal operation, destroying the coating and potentially creating a fire hazard. Always verify the substrate's operational temperature before specifying.
MD,
            ],

            [
                'title'   => 'Water-Based Heat-Resistant Paints: Formulation Advances and Environmental Compliance',
                'summary' => 'How waterborne technology is closing the performance gap with solvent-borne systems and meeting increasingly strict VOC regulations globally.',
                'tags'    => ['waterborne coating', 'VOC compliance', 'silicone emulsion', 'low-VOC', 'environmental regulation'],
                'content' => <<<MD
# Water-Based Heat-Resistant Paints: Formulation Advances and Environmental Compliance

Solvent-borne silicone coatings have historically dominated the heat-resistant market, but tightening VOC regulations in Europe (EU Directive 2004/42/CE, DE-17 BImSchV), North America (US EPA NESHAP), and increasingly in China (GB 18582-2020) are driving a rapid shift to waterborne formulations.

## How Waterborne Heat-Resistant Coatings Work

Silicone resins are inherently hydrophobic, so waterborne grades are produced as:
1. **Silicone emulsions** — silicone polymer dispersed in water with surfactants
2. **Silicone-acrylate copolymer dispersions** — better film formation at ambient temperatures
3. **Inorganic silicate dispersions** — alkali silicate binders for the highest temperature ranges (> 800 °C)

## Performance Comparison (2025)

| Property | Solvent-borne silicone | Waterborne silicone |
|---|---|---|
| VOC content | 300 – 600 g/L | 20 – 80 g/L |
| Max service temp | 600 °C | 550 °C (leading products) |
| Film formation temp | –10 to 5 °C | 5 – 10 °C (requires coalescent) |
| Adhesion to steel | Excellent | Very Good |
| Drying time | 30 – 60 min | 60 – 90 min |
| Application by brush | Good | Good |
| Application by HVLP | Excellent | Good (viscosity adjustment needed) |

## Regulatory Landscape

- **EU (Annex II, subcategory A/i)**: Limit 30 g/L for decorative; industrial coatings limited separately under IPPC
- **China GB 18582-2020**: VOC ≤ 420 g/L for solvent-borne primers; drives adoption of waterborne
- **IMO PSPC**: Ballast tank coatings favour low-VOC for confined-space application

## Leading Waterborne Products (2024–2025)

- **Hempel Hempadur Heat 56940** (waterborne): rated to 400 °C, VOC < 50 g/L
- **Jotun Thermoprime WB**: primer rated to 500 °C, part of Jotun's Resist range
- **Sherwin-Williams Thermaclad WB**: 540 °C rated, VOC < 80 g/L, suitable for industrial stacks

## Formulation Challenges Remaining

- **Freeze-thaw stability** — waterborne emulsions are sensitive to cold storage; glycol additions are used but add VOC
- **Humidity application window** — cannot apply in rain or > 85 % RH
- **Film continuity** — waterborne films are more susceptible to pinholes at low DFT
MD,
            ],

            [
                'title'   => 'Nanotechnology in Heat-Resistant Coatings: Nano-Fillers, Properties, and 2025 Research Status',
                'summary' => 'A review of how nano-sized particles — silica, graphene, alumina, and CNTs — are enhancing high-temperature coating performance.',
                'tags'    => ['nanotechnology', 'nano-filler', 'graphene coating', 'carbon nanotubes', 'silica nanoparticles'],
                'content' => <<<MD
# Nanotechnology in Heat-Resistant Coatings: Nano-Fillers, Properties, and 2025 Research Status

Nanomaterials — particles with at least one dimension below 100 nm — offer extraordinary surface-area-to-volume ratios that create properties impossible to achieve with conventional fillers. In heat-resistant coatings, several nano-additives are reaching commercial maturity.

## Key Nano-Additives and Their Effects

### 1. Fumed (Nano) Silica
- Particle size: 5–50 nm
- **Reinforcement**: Increases scratch hardness (pencil hardness +2 units typical)
- **Thixotropy**: Excellent anti-sag agent for vertical surfaces
- **Thermal stability**: Improves silicone film retention at high temperatures
- Commercial level: **Mature, widely used**

### 2. Nano Alumina (Al₂O₃)
- Particle size: 10–80 nm
- **Wear resistance**: Mohs hardness of alumina (9) translates to improved abrasion resistance
- **Thermal barrier**: Reduces thermal conductivity at 3–8 wt % loading
- **Challenge**: Poor dispersion without surface treatment (silane coupling agents)
- Commercial level: **Commercial, niche premium products**

### 3. Graphene Oxide (GO) / Reduced Graphene Oxide (rGO)
- Platelet thickness: ~1 nm; lateral size: 1–10 µm
- **Barrier properties**: Tortuous path for oxygen/water vapour reduces corrosion rate significantly
- **Thermal conductivity**: Enhances in-plane heat spreading (useful for heat dissipation coatings)
- **Challenge**: Agglomeration, cost, and regulatory uncertainty (ECHA nano-REACH review 2025)
- Commercial level: **Early commercial** (Graphenstone, G6 Materials)

### 4. Carbon Nanotubes (CNTs)
- Diameter: 1–20 nm
- **Mechanical reinforcement**: Tensile strength improvement
- **EMI shielding**: Useful in electronics heat-resistant applications
- **Challenge**: Dispersion, cost, fibre-classification regulatory questions
- Commercial level: **R&D / limited commercial**

### 5. Nano-Clay (Montmorillonite)
- Platelet thickness: ~1 nm
- **Barrier** and **flame retardancy** (char stabiliser)
- Well-established in polymers; less dominant in heat-resistant coatings
- Commercial level: **Established in some formulations**

## 2025 Research Highlights

- **Graphene-silicone hybrid coatings** (Tsinghua University, 2024): 30 % reduction in oxygen permeability and 50 °C increase in effective service temperature versus reference silicone.
- **rGO-phosphate coatings for 1 000 °C service** (Fraunhofer IFAM, 2024): Demonstrated on gas turbine components.
- **Self-healing nano-coating** (NIST, 2025 pre-print): Encapsulated silicone precursors in nanocapsules rupture at crack sites and re-form film — could extend maintenance intervals by 3–5×.

## Practical Considerations

Nano-fillers require specific health & safety controls (respiratory protection, nano-specific COSHH/REACH registration) and add cost of €5–50/kg versus conventional fillers. Verify nano-registration status before importing or using in jurisdictions with nano-specific regulations.
MD,
            ],

            [
                'title'   => 'Heat-Resistant Coatings for Petrochemical and Power Generation: Specifications and Case Studies',
                'summary' => 'How refineries, chemical plants, and power stations specify, apply, and inspect heat-resistant coatings on critical process equipment.',
                'tags'    => ['petrochemical', 'power generation', 'NACE', 'boiler coating', 'process vessel', 'insulation under coating'],
                'content' => <<<MD
# Heat-Resistant Coatings for Petrochemical and Power Generation: Specifications and Case Studies

The petrochemical and power generation industries are among the largest consumers of heat-resistant coatings, driving demand for robust specifications, rigorous QC, and long service-life guarantees (15–25 years on critical equipment).

## Critical Equipment Categories

| Equipment | Operating Temp | Primary Concern |
|---|---|---|
| Atmospheric distillation column | 350 – 400 °C | Sulphidation corrosion, CUI |
| FCC riser / regenerator | 500 – 700 °C | High-velocity erosion, refractory loss |
| Boiler waterwalls & superheaters | 450 – 600 °C | Fireside corrosion, fly-ash erosion |
| Gas turbine exhaust ducts | 500 – 650 °C | Oxidation, thermal fatigue |
| Stack liners | 200 – 500 °C | Acid dew-point condensate (H₂SO₄) |

## Corrosion Under Insulation (CUI)

CUI is a pervasive, high-cost failure mode on insulated piping and vessels at 60–175 °C (where water condenses under the insulation). The correct strategy is:
1. Apply a heat-resistant coating (e.g., epoxy phenolic or modified silicone) as a CUI-resistant primer
2. Apply insulation with weather-resistant cladding
3. Seal all penetrations to prevent water ingress

**NACE SP0198** and **ISO 19277** provide the primary specification framework for CUI prevention coatings.

## Specification Writing: Key Parameters to Define

A complete coating specification for high-temperature service should state:
- Blast standard and anchor profile (µm Rz)
- Coating system (primer + intermediate + topcoat) with product names and DFT per coat
- Minimum/maximum DFT and inspection method (wet film gauge, dry film gauge)
- Application conditions (temperature, humidity, dew point)
- Cure schedule
- Holiday (pinhole) detection method and acceptance criteria
- Third-party inspection hold points

## Case Study: Ethylene Cracker Transfer Line (Middle East, 2023)

**Problem**: Cracker transfer lines operating at 850 °C had phosphate-bonded coating failing within 18 months due to vibration-induced micro-cracking.

**Solution**: Switched to a ceramic fibre mat overlay system (mechanically fixed) in the highest-temperature zone, with a silicone-ceramic hybrid coating on the outer surface for appearance and corrosion protection.

**Result**: Three-year inspection (2026 planned) expected to show zero degradation. Coating system cost was 40 % higher but maintenance savings justified the premium.

## Inspection Methods

- **DFT**: Elcometer 456 or equivalent (ASTM D7091)
- **Holiday detection**: Low-voltage wet-sponge (≤ 500 µm DFT) per NACE SP0188
- **Adhesion**: Pull-off test per ISO 4624, minimum 5 MPa for structural applications
- **Visual**: ISO 4628 series for blistering, cracking, and rusting
MD,
            ],

            [
                'title'   => 'Health, Safety, and Environmental Compliance for Heat-Resistant Paint Application (2025)',
                'summary' => 'Current HSE requirements, exposure limits, and best practices for safe application of high-temperature coatings in industrial settings.',
                'tags'    => ['HSE', 'VOC', 'REACH', 'COSHH', 'application safety', 'respirator', 'isocyanate-free'],
                'content' => <<<MD
# Health, Safety, and Environmental Compliance for Heat-Resistant Paint Application (2025)

The regulatory environment for industrial coatings is tightening globally. Applicators, specifiers, and employers must keep pace with evolving substance restrictions, exposure limits, and waste regulations.

## Key Hazardous Substances in Heat-Resistant Paints

| Substance | Hazard | Regulation |
|---|---|---|
| Aromatic solvents (xylene, toluene) | Neurotoxin, reproductive toxin | REACH SVHC (xylene); many products reformulating |
| Chromate pigments | Carcinogen (Cr VI) | Banned in EU (REACH Annex XVII); restricted in US (OSHA 1910.1026) |
| Lead-based pigments | Neurotoxin | Banned in most countries; abatement regulations apply to old coatings |
| Crystalline silica (filler) | Silicosis risk | OSHA PEL 0.05 mg/m³ (respirable); UK WEL 0.1 mg/m³ |
| Organotins (catalysts) | Endocrine disruptor | EU restriction under REACH |

## Personal Protective Equipment (PPE) Requirements

**Spraying operations:**
- Full-face air-fed respirator (PAPR or supplied-air) where LEV is insufficient
- Chemical-resistant gloves (nitrile minimum 0.2 mm, solvent-borne products)
- Coverall and eye protection

**Brush/roller application:**
- P3 or equivalent particle filter if sanding is involved
- Chemical-resistant gloves
- Eye protection

## OEL / WEL Reference Values (2025)

| Substance | OEL (8-hr TWA) | Region |
|---|---|---|
| Xylene | 50 ppm | EU IOELV (2017/164/EU) |
| Xylene | 100 ppm | OSHA PEL |
| Total VOC (indicative) | — | No universal OEL; use LEV |
| Silicone hydrolysis products | — | No established OEL; use ventilation |

## Environmental Disposal and Waste

- **Solvent-borne waste**: Classified as hazardous waste; must be collected by licensed waste contractor (EWC code 08 01 11)
- **Water-borne rinse water**: May require effluent treatment before drain discharge (pH neutralisation, settleable solids)
- **Empty aerosol cans**: Depressurise fully before disposal; may be recycled as metal

## Emerging Regulations (2025)

- **EU PFAS restriction (ECHA 2025)**: Broad restriction on per- and polyfluoroalkyl substances being finalised; affects some specialist high-temperature release coatings using PTFE or PFPE additives
- **EU Taxonomy and Green Deal**: Pressure on coating manufacturers to disclose Scope 3 emissions and provide Environmental Product Declarations (EPDs) for procurement decisions
- **China Double Carbon Policy**: VOC solvent limits in industrial coatings tightened under the 14th Five-Year Plan; driving adoption of waterborne and UV-cure systems

## Curing Fumes

During the thermal cure of silicone coatings, some formulations release:
- **Acetic acid** (acetoxy-cure systems) — pungent, irritant
- **Methanol or ethanol** (alkoxy-cure systems) — asphyxiant in high concentrations

Ensure adequate ventilation during the first operational heat-up and warn personnel in the area.
MD,
            ],

        ];

        foreach ($entries as $entry) {
            BasicKnowledgeTrend::updateOrCreate(
                ['title' => $entry['title']],
                [
                    'category_id' => $category->id,
                    'status'      => 'published',
                    'summary'     => $entry['summary'],
                    'tags'        => $entry['tags'],
                    'content'     => $entry['content'],
                ]
            );
        }
    }
}
