<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class VanadiumKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'science')->first();

        if (!$category) {
            $this->command->warn('Science category not found. Run DatabaseSeeder first.');
            return;
        }

        BasicKnowledgeTrend::updateOrCreate(
            ['title' => 'Vanadium: Properties, Uses, and Scientific Importance'],
            [
                'category_id' => $category->id,
                'summary'     => 'Vanadium (V, atomic number 23) is a hard, silvery-grey transition metal prized for strengthening steel, enabling next-generation energy storage batteries, and playing subtle but essential roles in biology. This guide walks beginners through what vanadium is, where it comes from, how it behaves chemically, and why it matters in industry and science.',
                'content'     => <<<'MD'
## What Is Vanadium?

Vanadium is a chemical element with the symbol **V** and atomic number **23**. It sits in Group 5 of the periodic table, in the middle of the transition metals — the large block of metallic elements that spans the middle of the table. Its atomic mass is approximately 50.94 u (unified atomic mass units).

At room temperature, pure vanadium is a **hard, silvery-grey metal** that is both ductile (can be drawn into wire) and malleable (can be hammered into sheets). It has a body-centred cubic (BCC) crystal structure, the same atomic arrangement as iron at room temperature.

The element was discovered in 1801 by the Spanish-Mexican mineralogist Andrés Manuel del Río, who initially called it "erythronium" because its salts turned red on heating. Swedish chemist Nils Gabriel Sefström rediscovered it independently in 1830 and named it after **Vanadis**, the Norse goddess of beauty, because of the remarkable range of vivid colours vanadium compounds display — from pale yellow and green through deep blue to intense purple and bright orange.

---

## Key Physical Properties

| Property | Value |
|---|---|
| Atomic number | 23 |
| Symbol | V |
| Atomic mass | 50.942 u |
| Melting point | 1 910 °C (3 470 °F) |
| Boiling point | 3 407 °C (6 165 °F) |
| Density (solid) | 6.11 g/cm³ |
| Crystal structure | Body-centred cubic (BCC) |
| Electrical resistivity | 197 nΩ·m at 20 °C |
| Hardness (Mohs) | 7.0 |

**What these numbers mean for a beginner:**
- The melting point (1 910 °C) is much higher than iron (1 538 °C), which tells you vanadium is extremely heat-resistant — useful in demanding engineering environments.
- The density (6.11 g/cm³) is about 78% that of iron, meaning vanadium is notably lighter for the same volume. This matters in aerospace, where every gram counts.
- A Mohs hardness of 7 puts it harder than glass (5.5) but softer than quartz (7), so it resists scratching without being as brittle as ceramics.

---

## Chemical Properties and Oxidation States

Vanadium is famous among chemists for having **four stable oxidation states**: +2, +3, +4, and +5. An oxidation state describes how many electrons an atom has effectively gained or lost when bonded to other atoms. Most elements have only one or two stable states; vanadium's four states make it unusually versatile.

Each oxidation state produces a **different colour** in aqueous (water) solution:

| Oxidation state | Ion / species | Colour in water |
|---|---|---|
| +2 | V²⁺ | Violet / lavender |
| +3 | V³⁺ | Green |
| +4 | VO²⁺ (vanadyl ion) | Blue |
| +5 | VO₄³⁻ (vanadate) | Yellow–orange |

This colour-changing behaviour makes vanadium solutions a classic demonstration in undergraduate chemistry laboratories. By adding a reducing agent (such as zinc metal and dilute acid), students can watch a solution cycle through all four colours as the oxidation state drops from +5 down to +2.

**Electrochemically**, the step-wise reduction potentials are:
- VO₂⁺/VO²⁺ (V⁵⁺/V⁴⁺): +1.00 V
- VO²⁺/V³⁺ (V⁴⁺/V³⁺): +0.36 V
- V³⁺/V²⁺: −0.26 V

These well-spaced potentials are precisely why vanadium is ideal for redox flow batteries (see Uses below).

**Corrosion resistance:** At room temperature vanadium forms a thin, adherent oxide layer (V₂O₅) that protects the underlying metal from further oxidation, similar to the way aluminium passivates. This means bulk vanadium does not rust the way iron does, though it will slowly oxidise at elevated temperatures.

---

## Where Vanadium Is Found

Vanadium is the 20th most abundant element in Earth's crust at approximately **120 parts per million (ppm)** — more common than copper (68 ppm) or zinc (75 ppm), though far less common than iron (56 000 ppm). It does not occur as a free metal in nature; it is always found combined with other elements.

**Major mineral sources:**
- **Patronite (VS₄)** — a vanadium sulphide found in Peru; historically important as one of the first commercial ores.
- **Vanadinite [Pb₅(VO₄)₃Cl]** — a brilliant red or orange lead-vanadium mineral. Its vivid colour makes it prized by mineral collectors as well as being an ore.
- **Carnotite [K₂(UO₂)₂(VO₄)₂·3H₂O]** — a yellow uranium-vanadium mineral found in sedimentary deposits in the Colorado Plateau (USA); historically significant because mining it for uranium also yielded vanadium as a by-product.
- **Roscoelite** — a vanadium-rich mica.

In practice, the majority of the world's vanadium today comes as a **by-product of steel production**: vanadium concentrates naturally in certain iron ores and in crude oil (particularly Venezuelan and Canadian heavy crude), and it accumulates in the slag and residues left after smelting. China, Russia, and South Africa are the three largest producing countries, together accounting for over 90% of global output.

Vanadium is also found in small concentrations in:
- Seawater (~2 parts per billion)
- Certain living organisms, particularly **sea squirts (tunicates)**, which accumulate vanadium in their blood cells at concentrations up to 10 million times greater than seawater — a biological mystery that still intrigues researchers.

---

## Industrial Uses

### 1. Steel Alloys (by far the largest use — ~90% of all vanadium consumed)

Adding small amounts of vanadium (typically 0.1–0.2% by weight) to steel produces **high-strength low-alloy (HSLA) steel**, sometimes called "microalloyed steel." The vanadium combines with carbon and nitrogen in the steel to form tiny, hard particles called **vanadium carbides (V₄C₃)** and **vanadium nitrides (VN)** that are only a few nanometres across.

These nano-sized particles act as "grain refiners" — they pin the boundaries between individual steel crystals (grains) and prevent them from growing large. Smaller grains mean:
- **Higher tensile strength** (the steel resists being pulled apart)
- **Higher yield strength** (the steel resists permanent deformation)
- **Better toughness** (the steel absorbs impact without cracking)

The result: HSLA steel with vanadium can be two to three times stronger than plain carbon steel of the same weight. This allows engineers to use **thinner, lighter sections** while maintaining safety, which directly reduces material consumption and cost. Applications include:
- Structural steel for skyscrapers, bridges, and stadiums
- Automotive chassis and crash safety structures
- Pipelines for oil, gas, and water
- Rail track and rolling stock
- Tools, springs, and cutting blades

**Historical note:** Charles Martin Hall and Henry Ford were early adopters of vanadium steel around 1905–1908. Ford's Model T used vanadium steel for its crankshaft and connecting rods, enabling a lighter, stronger car than competitors could build.

### 2. Vanadium Redox Flow Batteries (VRFBs)

A vanadium redox flow battery stores electrical energy in two liquid electrolytes — both containing vanadium ions dissolved in sulphuric acid, but at different oxidation states. During charging, V²⁺/V³⁺ forms in the negative tank and V⁴⁺/V⁵⁺ forms in the positive tank; during discharging, the process reverses, passing electrons through an external circuit.

**Why vanadium is uniquely suited to this design:**
- Using vanadium on *both* sides of the battery eliminates cross-contamination problems (if electrolyte leaks across the membrane, it is simply vanadium on vanadium).
- The electrolyte lasts virtually indefinitely and can be fully regenerated, unlike the electrodes in lithium-ion batteries.
- Capacity (energy stored) and power (rate of delivery) are independently scalable by changing tank size versus cell stack size.

VRFBs are too heavy and bulky for vehicles, but they are ideal for **grid-scale stationary energy storage** — storing surplus electricity from solar and wind farms and releasing it when demand peaks. Projects of 100 MWh or more are now operating in China, Japan, the United States, and Europe. As renewable energy generation grows, VRFBs represent one of the most promising long-duration storage technologies.

### 3. Titanium Alloys

Vanadium's most famous non-steel metallurgical role is in **Ti-6Al-4V** (titanium with 6% aluminium and 4% vanadium), the single most widely used titanium alloy in the world. The vanadium stabilises the high-temperature beta phase of titanium at room temperature, giving the alloy excellent strength, fatigue resistance, and corrosion resistance. It is the standard material for:
- Jet engine components (fan blades, compressor discs)
- Airframe structural parts
- Medical implants (hip/knee replacements, bone screws)
- High-performance sporting equipment

### 4. Catalysis

Vanadium pentoxide (V₂O₅) is the primary industrial catalyst for the **Contact Process** — the production of sulphuric acid, the world's highest-volume industrial chemical. In the Contact Process, SO₂ (sulphur dioxide) is oxidised to SO₃ (sulphur trioxide) over a V₂O₅ catalyst at 450–550 °C:

2 SO₂ + O₂ → 2 SO₃

V₂O₅ is used because it can cycle reversibly between the +4 (V₂O₄) and +5 (V₂O₅) oxidation states, accepting and donating oxygen atoms to drive the reaction. This produces roughly 250 million tonnes of sulphuric acid per year globally for fertilisers, chemical manufacturing, and mineral processing.

### 5. Other Uses

- **Vanadium foil** is used as a bonding layer in titanium–steel cladding for pressure vessels and heat exchangers, because it is one of the few metals that bonds strongly to both.
- **Vanadium-gallium superconducting alloys** (V₃Ga) were historically used in superconducting magnets before being largely replaced by niobium-based materials.
- **Vanadium-doped glass** creates a deep blue-green colour used in decorative and specialty optical applications.

---

## Biological and Environmental Role

### Vanadium in Living Organisms

Vanadium is considered a **trace element** in nutrition — required in extremely small amounts by some organisms and possibly beneficial in others. Key biological facts:

- **Vanadium nitrogenases:** Certain nitrogen-fixing bacteria (notably some species of *Azotobacter*) use vanadium-containing nitrogenase enzymes as an alternative to the more common molybdenum nitrogenase. These vanadium enzymes fix atmospheric nitrogen (N₂) into ammonia at lower temperatures, making them ecologically important in cold soils and environments where molybdenum is scarce.

- **Haloperoxidases:** Many marine algae and fungi use vanadium-containing haloperoxidase enzymes to produce halogenated organic compounds (compounds containing chlorine, bromine, or iodine). These reactions play roles in chemical defence, signalling, and the global cycling of halogens.

- **Tunicates:** Sea squirts (ascidians) accumulate vanadium in specialised blood cells called vanadocytes, sometimes reaching millimolar concentrations — millions of times more concentrated than the surrounding seawater. The biological function is debated; proposed roles include defence, structural support, and oxygen transport analogous to haemoglobin. Their vanadium-binding proteins (vanabins) are an active area of biochemical research.

- **Human nutrition:** There is no established Recommended Dietary Allowance (RDA) for vanadium in humans, but typical dietary intake is 10–60 micrograms per day from grains, mushrooms, shellfish, and black pepper. Research in diabetic animal models showed that vanadyl sulphate (VOSO₄) can mimic some actions of insulin, stimulating glucose uptake. Human trials have been conducted, but results are inconclusive and vanadium supplements are not currently recommended as a diabetes treatment.

### Toxicology

Vanadium compounds are toxic at high doses. The primary route of occupational exposure is inhalation of vanadium dust or fumes during smelting, welding, or the burning of heavy fuel oil. Symptoms of vanadium poisoning include respiratory irritation, a characteristic **green tongue** (from vanadium salts deposited on mucous membranes), and, at very high exposures, pulmonary damage. Regulatory limits for occupational exposure are typically set at 0.05 mg/m³ (as V₂O₅ dust) or lower. In the environment, vanadium from industrial emissions and oil combustion accumulates in soils and aquatic sediments, where it can affect invertebrate and fish populations at elevated concentrations.

---

## Scientific Importance

### 1. Textbook Example of Oxidation States

Vanadium's four stable, colourful, and easily interconvertible oxidation states make it one of the most pedagogically important elements in inorganic chemistry. Demonstrating the sequential colour changes from V⁵⁺ (yellow) → V⁴⁺ (blue) → V³⁺ (green) → V²⁺ (violet) using zinc amalgam reduction is a standard undergraduate experiment that makes abstract electrochemical concepts visually vivid and intuitive.

### 2. Coordination Chemistry

Vanadium forms a rich variety of coordination complexes — molecules where the vanadium atom is surrounded by other atoms or molecules (called ligands) that donate electron pairs to it. These complexes display a fascinating range of structures, magnetic properties, and reactivity. Vanadium's +4 oxidation state (the vanadyl ion, VO²⁺) is particularly distinctive: the strong V=O double bond makes the vanadyl group behave almost as a single "super-ligand," profoundly influencing the geometry and reactivity of complexes that contain it. This makes vanadyl complexes important models for understanding enzyme active sites and for developing new catalysts and materials.

### 3. Energy Storage Research

Vanadium redox flow batteries are at the forefront of electrochemical energy storage research, driven by the global need for grid-scale storage to accommodate variable renewable energy. Active research areas include:
- Higher-concentration electrolytes to increase energy density
- Mixed-acid electrolytes to widen the operating temperature range
- New membrane materials to reduce cost and improve selectivity
- Hybrid flow battery designs coupling VRFBs with other chemistries

Understanding vanadium electrochemistry at the molecular level — how V⁵⁺/V⁴⁺ and V³⁺/V²⁺ couples behave in concentrated sulphuric acid, how electrolyte speciation changes with temperature and concentration, and how side reactions degrade performance — is an active area of physical and computational chemistry research.

### 4. Geochemistry and Palaeoclimate

Vanadium is increasingly used as a **palaeoredox proxy** in sedimentary geochemistry. Vanadium behaves very differently under oxidising versus reducing conditions: in oxygenated seawater it stays dissolved as mobile vanadate (VO₄³⁻), but under anoxic (oxygen-free) conditions it is reduced to V⁴⁺ or V³⁺ and precipitates into sediment. By measuring vanadium concentrations in ancient shales, geochemists can reconstruct the extent of ocean anoxia through geological time, helping to understand mass extinction events and ancient climate states.

---

## Summary for Beginners

| Topic | Key takeaway |
|---|---|
| What it is | Transition metal, element 23, symbol V, hard and silvery |
| Why it's colourful | Four stable oxidation states each produce a different colour in solution |
| Biggest industrial use | Strengthening steel (HSLA steel for construction, cars, pipelines) |
| Exciting new use | Vanadium redox flow batteries for renewable energy storage |
| In biology | Essential in some bacteria and algae; concentrated mysteriously in sea squirts |
| In the lab | Classic teaching example of electrochemistry and coordination chemistry |

Vanadium is a quietly important element — rarely discussed in popular science but embedded in the steel of the buildings we inhabit, the pipelines carrying our energy, the titanium joints replacing worn hips, and now the batteries that may help power a renewable energy future.
MD,
                'tags'        => ['vanadium', 'transition metals', 'chemistry', 'metallurgy', 'energy storage', 'HSLA steel', 'redox flow battery', 'periodic table', 'oxidation states', 'beginner'],
                'status'      => 'published',
            ]
        );

        $this->command->info('Seeded 1 BasicKnowledgeTrend entry: Vanadium.');
    }
}
