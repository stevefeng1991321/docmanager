<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class RefractoryGoldKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'science')->first();

        if (!$category) {
            $this->command->warn('Science category not found. Run DatabaseSeeder first.');
            return;
        }

        foreach ($this->entries($category->id) as $entry) {
            BasicKnowledgeTrend::updateOrCreate(
                ['title' => $entry['title']],
                $entry
            );
        }

        $this->command->info('Seeded 20 BasicKnowledgeTrend entries: Refractory Gold.');
    }

    private function entries(int $categoryId): array
    {
        $tags = ['refractory gold', 'gold processing', 'mineralogy', 'hydrometallurgy', 'geochemistry'];

        return [

            // ── 1 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['definition', 'classification']),
                'title'       => 'Refractory Gold: Definition, Classification, and Economic Significance',
                'summary'     => 'Refractory gold ores are those from which gold cannot be economically recovered by standard cyanidation without a pre-treatment step. They account for roughly one-third of the world\'s gold reserves and are increasingly important as easy free-milling deposits are depleted.',
                'content'     => <<<'MD'
## What Makes Gold "Refractory"?

In gold metallurgy, an ore is called **refractory** when direct cyanide leaching — the global industry standard — recovers less than roughly 80% of the contained gold, even after fine grinding. The term comes from the Latin *refractarius* (stubborn, obstinate), and aptly describes ores that resist conventional extraction.

Refractoriness is not a binary property but a continuum. Industry practitioners often use the following recovery thresholds as a working classification:

| Recovery by direct cyanidation | Classification |
|---|---|
| > 90% | Free-milling |
| 80–90% | Mildly refractory |
| 40–80% | Moderately refractory |
| < 40% | Highly refractory |

These thresholds are empirical and project-specific; a deposit that is "mildly refractory" at one gold price may become economically free-milling when the gold price rises enough to tolerate higher unit operating costs.

## Root Causes of Refractoriness

Three distinct mechanisms cause refractoriness, and a single ore body can exhibit all three simultaneously:

**1. Physical encapsulation in sulfide minerals.** Gold particles — even when large enough to be visible under a microscope — are completely surrounded by pyrite (FeS₂), arsenopyrite (FeAsS), pyrrhotite (Fe₁₋ₓS), or other sulfide grains. Cyanide solution cannot reach the gold because it cannot diffuse rapidly through a dense sulfide crystal. No amount of conventional grinding dissolves the sulfide host; only chemical oxidation or extreme ultra-fine grinding can liberate the gold.

**2. Submicroscopic (invisible) gold.** Gold occurs as particles smaller than ~0.1 µm — below the resolution of standard optical microscopy and even scanning electron microscopy — either as solid-solution gold (Au atoms substituted into the sulfide crystal lattice) or as discrete nanoparticles within lattice defects or grain boundaries. Cyanide cannot access gold that is atomically dispersed within a crystal.

**3. Preg-robbing by carbonaceous matter.** Naturally occurring organic carbon in the ore (kerogen, graphite, bitumen) adsorbs gold-cyanide complexes [Au(CN)₂⁻] from solution just as strongly as the activated carbon used intentionally in Carbon-in-Leach (CIL) circuits. The carbon competes with the recovery circuit, stripping gold back out of solution and onto the ore itself.

## Economic Importance

Refractory ores represent approximately **30–35% of global gold reserves** by contained metal. As high-grade, easily processed free-milling deposits are progressively mined out, the industry's reserve base shifts toward refractory sources. Major refractory gold provinces include:

- **Carlin Trend, Nevada, USA** — sediment-hosted, arsenian pyrite, strongly carbonaceous; the largest gold-producing region in North America
- **Sukhoi Log, Russia** — one of the world's largest undeveloped gold deposits; primarily arsenopyrite-hosted
- **Obuasi, Ghana** — arsenopyrite with significant carbonaceous gangue
- **Olimpiada, Russia** — double refractory (arsenopyrite + carbonaceous matter)
- **Kumtor, Kyrgyzstan** — sulfide-hosted with preg-robbing carbon

Processing refractory ores requires capital-intensive pre-treatment plants (pressure oxidation, roasting, or biooxidation) that can add USD 150–400/oz to all-in sustaining costs. Despite this, dozens of refractory gold mines operate profitably globally, and the technology for treating these ores continues to improve.

## Why It Matters Now

With global average gold ore grades declining from ~2 g/t in the 1990s to below 1 g/t today at many operations, the ability to economically treat refractory ores is no longer a niche capability — it is central to the industry's ability to replace reserves. Understanding the science of refractoriness is therefore foundational to modern gold metallurgy.
MD,
            ],

            // ── 2 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['pyrite', 'arsenopyrite', 'sulfide mineralogy']),
                'title'       => 'Pyrite-Hosted Gold: Mineralogy, Gold Siting, and Deportment',
                'summary'     => 'Pyrite (FeS₂) is the single most important host mineral for refractory gold worldwide. Gold is distributed across multiple siting types within pyrite — from visible inclusions to atomically dispersed solid-solution — and the relative proportions of these populations control process choice and expected recovery.',
                'content'     => <<<'MD'
## Pyrite as a Gold Host

Pyrite (iron disulfide, FeS₂) is the most abundant sulfide mineral on Earth and the dominant host of refractory gold in sediment-hosted, orogenic, and intrusion-related gold systems. Its cubic crystal structure (space group Pa3̄) and disulfide dumbbell units (S₂²⁻) create a framework that can accommodate gold in several distinct ways, each with very different metallurgical implications.

## Crystal Chemistry

Pyrite has the **marcasite-type** structure with Fe²⁺ in a slightly distorted octahedral site coordinated by six sulfur atoms, and paired S₂²⁻ dumbbells occupying the anion positions. The unit cell parameter is a = 5.416 Å.

Gold can enter pyrite through two distinct mechanisms:

**Lattice substitution (solid-solution gold, Au⁺):** Au⁺ (ionic radius 1.37 Å) substitutes for Fe²⁺ (0.78 Å) in the octahedral site at extremely low concentrations — typically < 1000 ppm by weight. Simultaneously, As³⁻ (1.58 Å) substitutes for S²⁻ (1.84 Å). The coupled substitution Au⁺ + As³⁻ ↔ Fe²⁺ + S²⁻ maintains charge balance and explains the well-documented positive correlation between Au and As in refractory pyrite (the "invisible gold" phenomenon first systematically described by Simon et al., 1999, and Reich et al., 2005).

**Nanoscale gold particles:** When the gold concentration in the local hydrothermal fluid exceeded the saturation limit for solid-solution gold, metallic Au⁰ nanoparticles (1–100 nm) precipitated within lattice defects, microfractures, and grain boundaries of the growing pyrite crystal. These nanoparticles are not recoverable by cyanidation unless the pyrite host is destroyed, because the nanoparticle diameter is far below the threshold for cyanide diffusion to be kinetically effective.

## The Au–As Solubility Limit

Reich et al. (2005, *Geochimica et Cosmochimica Acta*) defined an empirical solubility limit for gold in arsenian pyrite:

**log[Au]ₛₒₗ = 0.47 × log[As] − 1.50** (concentrations in ppm)

Pyrite plotting below this line in Au–As space contains gold primarily in solid solution (Au⁺); pyrite plotting above it contains gold as nanoparticles (Au⁰). This relationship, derived from LA-ICP-MS data, is now a standard tool for predicting gold deportment from trace-element microanalysis without the need for TEM imaging.

## Pyrite Generations and Zoning

Refractory gold deposits typically contain multiple pyrite generations with contrasting gold contents:

- **Py1 (diagenetic/early pyrite):** Fine-grained, porous, often framboidal; commonly low in Au but high in As. Porosity facilitates solid-solution gold uptake during metamorphic fluid flow.
- **Py2 (hydrothermal overgrowth):** Euhedral, coarse-grained zones overgrown on Py1 cores during gold-mineralising events; typically the highest Au concentration; may contain visible gold inclusions at grain boundaries.
- **Py3 (late/barren overgrowth):** Clean, arsenic-poor, gold-poor rims precipitated from late-stage dilute fluids.

LA-ICP-MS mapping of individual pyrite grains routinely reveals concentric gold-rich zones corresponding to Py2, allowing geologists to target the most gold-rich pyrite populations during geometallurgical characterisation.

## Metallurgical Implications

| Gold siting in pyrite | Typical Au concentration | Recoverable by direct CN? | Required pre-treatment |
|---|---|---|---|
| Free gold inclusions > 1 µm | — | Partially (if grain boundary accessible) | Fine grinding may help |
| Nanoscale particles (1–100 nm) | 1–500 ppm | No | POX, roasting, or BIOX |
| Solid-solution Au⁺ | < 100 ppm | No | POX, roasting, or BIOX |

The proportion of gold in each siting category — determined by progressive leach tests, diagnostic leaching, or direct microanalysis — is the key input to pre-treatment plant design and economic assessment.
MD,
            ],

            // ── 3 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['arsenopyrite', 'As-Au correlation', 'crystal chemistry']),
                'title'       => 'Arsenopyrite-Hosted Gold: Crystal Chemistry and Au–As–S Phase Relationships',
                'summary'     => 'Arsenopyrite (FeAsS) is the second most important refractory gold host and uniquely capable of accommodating high concentrations of solid-solution gold. Its crystal chemistry, the Au–As–S phase diagram, and the relationship between As content and gold solubility are central to understanding Carlin-type and orogenic gold deposits.',
                'content'     => <<<'MD'
## Arsenopyrite Structure

Arsenopyrite (FeAsS) crystallises in the monoclinic system (space group P2₁/c) with alternating As and S atoms paired in dumbbell units coordinated to Fe atoms in a distorted octahedral geometry. The unit cell parameters are a = 5.744 Å, b = 5.674 Å, c = 5.785 Å, β = 112.17°.

The key feature distinguishing arsenopyrite from pyrite as a gold host is the **larger anion site** accommodating the As³⁻ ion (radius 1.58 Å vs. S²⁻ at 1.84 Å). This creates a more flexible lattice that tolerates higher concentrations of substituted Au⁺ before nanoparticle exsolution — meaning arsenopyrite can store more solid-solution gold per unit volume than pyrite.

## The Au–As–S Phase Diagram

The stability of arsenopyrite relative to pyrite, pyrrhotite, and native gold is governed by the As–Fe–S ternary system and is sensitive to temperature, ƒS₂ (sulfur fugacity), and ƒAs₂ (arsenic fugacity). The arsenopyrite stability field is bounded by:

- Low ƒAs₂ / high ƒS₂: pyrite + pyrrhotite stable, arsenopyrite decomposes
- High ƒAs₂ / low ƒS₂: arsenolite (As₂O₃) or native arsenic stable
- Temperature > ~500 °C: arsenopyrite decomposes to loellingite (FeAs₂) + pyrrhotite

Gold depositing from hydrothermal fluids tends to co-precipitate with arsenopyrite precisely in the temperature window (200–400 °C) and ƒS₂ range (10⁻¹² to 10⁻⁶ bar) where arsenopyrite is the stable Fe–As–S phase. This explains the universal positive correlation between arsenopyrite abundance and gold grade in orogenic and Carlin-type systems.

## Solid-Solution Capacity

Loucks & Mavrogenes (1999) demonstrated experimentally that gold solubility in arsenopyrite exceeds that in pyrite by approximately one order of magnitude under equivalent conditions. Natural arsenopyrite specimens from the Ashanti deposit (Ghana) and the Muruntau deposit (Uzbekistan) have been measured at up to 5000–8000 ppm Au (0.5–0.8 wt%) by bulk dissolution, with much of that gold confirmed in solid solution by SIMS.

The coupled substitution mechanism is analogous to that in pyrite:
**Au⁺ + As³⁻ ↔ Fe²⁺ + S²⁻ (or As³⁻)**

High-As arsenopyrite (As/(As+S) > 0.5, i.e., As-rich compositions approaching FeAs₂) consistently shows higher solid-solution gold contents, consistent with the substitution mechanism.

## Thermal Decomposition and Gold Release

During pressure oxidation and roasting (the two main pre-treatment routes), arsenopyrite decomposes through a sequence of reactions:

**At 400–550 °C (roasting):**
4 FeAsS + 7 O₂ → 4 FeAsO₄ · (partial) or → Fe₂O₃ + As₂O₃ + SO₂

The liberation of gold from the arsenopyrite lattice begins as soon as the crystal structure collapses, typically at 450–500 °C. However, if roasting temperature exceeds ~600 °C, ferric arsenate (FeAsO₄) sinters and can physically re-trap liberated gold — a "scorodite armoring" effect that is a key process-control concern.

**During pressure oxidation (POX):**
FeAsS + 13/4 O₂ + H₂O → FeOOH + H₃AsO₄ + SO₄²⁻

Gold is released as the arsenopyrite framework dissolves. Under high-temperature POX conditions (220–235 °C, >30 bar O₂), arsenic is immobilised as highly stable crystalline scorodite (FeAsO₄·2H₂O), addressing the environmental concern simultaneously with gold liberation.

## Analytical Characterisation

State-of-the-art characterisation of arsenopyrite-hosted gold uses:
- **LA-ICP-MS mapping:** Produces 2D elemental maps of Au, As, Fe, S, Co, Ni, Sb at spatial resolution of ~5 µm, revealing zonation patterns and correlation with Au.
- **EPMA (electron probe microanalysis):** Quantitative major-element compositions (Fe, As, S) with sub-µm spatial resolution.
- **SIMS (secondary ion mass spectrometry):** Sub-ppm detection of Au in lattice sites with ~15 µm spot size; distinguishes solid-solution from nanoparticulate contributions.
- **TEM (transmission electron microscopy):** Direct imaging of Au nanoparticles at atomic resolution; definitive proof of nanoparticle vs. solid-solution siting.
MD,
            ],

            // ── 4 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['carbonaceous matter', 'preg-robbing', 'organic carbon']),
                'title'       => 'Carbonaceous Matter and Preg-Robbing: Mechanisms, Measurement, and Mitigation',
                'summary'     => 'Preg-robbing is the adsorption of gold-cyanide complexes onto natural carbonaceous matter in the ore, directly competing with the recovery circuit. It is the defining challenge of "double refractory" ores and requires specific analytical protocols and processing strategies.',
                'content'     => <<<'MD'
## What Is Preg-Robbing?

In a standard cyanide leach, gold dissolves as the dicyanoaurate(I) complex:
**4 Au + 8 CN⁻ + O₂ + 2 H₂O → 4 [Au(CN)₂]⁻ + 4 OH⁻**

This complex is then recovered by adsorption onto activated carbon (Carbon-in-Leach or Carbon-in-Pulp circuits) or by zinc precipitation (Merrill-Crowe). In a preg-robbing ore, natural carbonaceous material present in the ore does the same job as activated carbon — it adsorbs [Au(CN)₂]⁻ from solution — but since it is part of the solid residue, the gold is lost to tails rather than recovered.

The term "preg-robbing" refers to the "pregnant" (gold-bearing) leach solution being robbed of its gold by the ore carbon.

## Types of Carbonaceous Matter

Not all carbon in ore is equally preg-robbing. The active forms are:

**Kerogen and bitumen:** Semi-mature organic matter derived from the thermal alteration of algal or bacterial organic material. Found in Carlin-type deposits (Nevada), where hydrothermal fluids remobilised carbonaceous matter from Paleozoic carbonaceous sediments. These are the most aggressively preg-robbing materials, with gold adsorption constants (K) comparable to activated carbon.

**Elemental carbon / graphite:** Crystalline or semi-crystalline graphite formed during metamorphism of organic-rich sediments. Present in Olimpiada (Russia), Obuasi (Ghana), and Sukhoi Log (Russia). Graphite is less preg-robbing than amorphous kerogen per unit surface area but can be present in large quantities.

**Char / anthracite:** Found in some contact-metamorphic settings. Intermediate adsorption capacity.

## Quantifying Preg-Robbing

**Bottle Roll Test with and without activated carbon competition:** The standard diagnostic involves leaching crushed ore with and without competing activated carbon. The difference in gold recovery indicates preg-robbing potential.

**Carbon Adsorption Index (CAI):** A synthetic [Au(CN)₂]⁻ solution at known concentration is contacted with the ore for a fixed time; the percentage of gold remaining in solution is measured. Lower gold in solution = higher preg-robbing.

**Petrographic characterisation:** Reflected-light microscopy and Raman spectroscopy distinguish graphite (sharp G and D bands at 1580 and 1350 cm⁻¹) from amorphous kerogen (broad, shifted peaks) and measure the degree of graphitisation (G/D peak ratio), which inversely correlates with adsorption capacity.

## The Double Refractory Problem

An ore that is simultaneously sulfide-locked (gold in arsenopyrite/pyrite) AND carbonaceous is called **double refractory**. This is particularly problematic because:

1. Grinding exposes more gold surface area and liberates more sulfide-hosted gold — but also generates more carbon surface area, worsening preg-robbing.
2. Oxidative pre-treatment (POX, roasting, biooxidation) destroys the sulfide hosts and releases gold, but it also activates carbonaceous matter (increases surface area and eliminates passivating organic coatings), often increasing the preg-robbing capacity of the carbon after pre-treatment.

The Olimpiada mine in Russia and the Twin Creeks mine in Nevada are canonical examples of double refractory processing challenges.

## Mitigation Strategies

**Blinding (passivation) of carbon:** Treatment with kerosene, diesel, light oil, or diesel/carbon disulfide blends coats the carbon surface and reduces its adsorption capacity. Used at some Carlin-type operations as a pre-leach conditioning step.

**Carbon-in-Leach (CIL) with high activated carbon addition:** Adding activated carbon in excess of the ore carbon's preg-robbing capacity shifts the adsorption equilibrium toward the activated carbon, which can then be stripped and gold recovered. Requires careful mass-balance of activated-carbon-to-ore-carbon ratio.

**Oxidative roasting of carbon prior to cyanidation:** Burning the carbon at 400–600 °C destroys the organic matter. Effective but generates CO₂ and, if sulfides are present, SO₂ — regulatory and cost concerns.

**Ultrafine grinding + ammoniacal thiosulfate leaching:** Avoids cyanide entirely; thiosulfate complexes [Au(S₂O₃)₂]³⁻ are not adsorbed by natural carbon as strongly as cyanide complexes, though thiosulfate consumption by carbonaceous matter remains a challenge.
MD,
            ],

            // ── 5 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['diagnostic leaching', 'characterisation', 'geometallurgy']),
                'title'       => 'Diagnostic Leaching: Sequential Extraction for Refractory Gold Ore Characterisation',
                'summary'     => 'Diagnostic leaching is a sequential chemical extraction protocol that quantifies how gold is distributed among free, sulfide-locked, and carbonaceous hosts within an ore sample. It is the industry-standard method for specifying pre-treatment requirements and predicting metallurgical performance.',
                'content'     => <<<'MD'
## Purpose and Principle

Diagnostic leaching answers a precise question: **where is the gold, and what is stopping us from recovering it?** By applying a series of selective chemical treatments in sequence — each dissolving a specific ore component — and measuring the gold released at each step, it quantifies the fraction of gold locked within each mineral host.

This quantitative deportment information directly drives process selection:
- High sulfide-locked gold → oxidative pre-treatment required
- High carbon-adsorbed gold → carbon passivation or alternative leanching
- High free gold → standard CIP/CIL without pre-treatment

## Standard Protocol (Loulo Protocol / SGS Modification)

The most widely used diagnostic leach sequence proceeds as follows, applied to pulverised head sample (typically 80% passing 75 µm):

**Step 1 — Direct cyanidation (48 h, pH 10.5, 200 ppm CN⁻):**
Dissolves all free (liberated) gold and any gold on sulfide grain surfaces. Gold in solution = free gold fraction.

**Step 2 — Carbon passivation + cyanidation:**
Pre-treat residue with light hydrocarbon to blind natural carbon; then re-leach with cyanide. Additional gold recovered = gold that was previously preg-robbed by natural carbon.

**Step 3 — Hydrogen peroxide oxidation + cyanidation:**
H₂O₂ in mild acid partially oxidises reactive sulfides (pyrrhotite, marcasite); subsequent cyanidation recovers gold from partially oxidised hosts. Gold recovered = gold in reactive (easily oxidised) sulfides.

**Step 4 — Bromine-methanol leach:**
BrCH₃OH is a powerful non-selective oxidant that dissolves all remaining gold, including gold in refractory pyrite and arsenopyrite. Gold recovered = gold in refractory sulfides.

**Step 5 — Acid digest of residue:**
Final total dissolution of residue confirms mass balance closure (should sum to ≥95% of head assay gold).

## Interpretation and Mass Balance

Results are reported as percentages of head gold falling into each category:

| Category | Typical range in refractory ores | Pre-treatment implication |
|---|---|---|
| Free (cyanide-soluble) | 10–40% | No pre-treatment for this fraction |
| Preg-robbed | 5–30% | Carbon passivation or CIL excess carbon |
| Reactive sulfide-locked | 5–20% | Mild oxidation or ultra-fine grinding |
| Refractory sulfide-locked | 30–70% | POX, roasting, or biooxidation |

A well-designed diagnostic leach closes the mass balance to within ±5% of the head assay, providing confidence in the deportment fractions.

## Limitations and Refinements

**Step 3/4 overlap:** H₂O₂ partially attacks arsenopyrite as well as reactive sulfides, creating ambiguity in the "reactive" vs. "refractory" sulfide fractions. Some laboratories use a separate nitric acid attack to specifically dissolve arsenopyrite before the bromine step.

**Spatial representativeness:** A single composite sample may mask heterogeneity. Modern geometallurgical programs run diagnostic leach on 30–100 samples from drill core to map spatial deportment variation across the ore body.

**Nanoparticle vs. solid-solution gold:** Standard diagnostic leaching cannot distinguish between gold in nanoparticles (Au⁰) and gold in solid solution (Au⁺) within a sulfide host — both are released in Step 4. Distinguishing them requires TEM or the Au–As solubility limit analysis (Reich et al., 2005).

## Integration with Process Design

Diagnostic leach data feed directly into the geometallurgical block model used for mine planning. Each block is assigned a predicted gold deportment profile, which in turn determines:
- Which ore parcels require pre-treatment
- The design capacity and chemistry of the pre-treatment plant
- Blending strategies to maintain consistent feed to the processing circuit
- Projected recovery and revenue for life-of-mine financial modelling
MD,
            ],

            // ── 6 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['pressure oxidation', 'POX', 'autoclave']),
                'title'       => 'Pressure Oxidation (POX): Process Chemistry, Autoclave Design, and Industrial Practice',
                'summary'     => 'Pressure oxidation (POX) uses high-temperature, high-pressure oxygen to oxidise sulfide minerals and liberate refractory gold. It is the dominant pre-treatment technology for high-grade refractory ores globally and produces a stable, cyanide-receptive oxidised residue with arsenic immobilised as scorodite.',
                'content'     => <<<'MD'
## Principle

Pressure oxidation is conducted in a horizontal, multi-compartment titanium-lined autoclave at 190–235 °C and 20–40 bar total pressure (with oxygen partial pressure typically 700–1400 kPa). Under these conditions, sulfide minerals oxidise rapidly — in minutes to hours — compared to weeks or months for biooxidation and the hours-to-days at atmospheric roasting.

## Key Reactions

**Pyrite oxidation:**
FeS₂ + 15/4 O₂ + 7/2 H₂O → Fe(OH)₃ + 2 H₂SO₄

**Arsenopyrite oxidation:**
FeAsS + 7/2 O₂ + H₂O → FeAsO₄ + H₂SO₄

At high-temperature POX (220–235 °C), iron precipitates as **hematite (Fe₂O₃)** rather than goethite/jarosite, and arsenic is co-precipitated as crystalline **scorodite (FeAsO₄·2H₂O)**. Scorodite is the most thermodynamically stable arsenic-bearing solid phase at ambient conditions and leaches arsenic at < 1 mg/L in standard TCLP tests, satisfying most regulatory disposal requirements.

At lower-temperature (low-temperature POX, 190–200 °C), the product is predominantly amorphous ferric arsenate, which is less stable than scorodite and requires careful pH control for long-term arsenic immobilisation.

## Acid Generation and Neutralisation

Sulfide oxidation generates sulfuric acid stoichiometrically. High-sulfide ores can produce strongly acidic autoclave discharge (pH 0–1). Before cyanidation, the slurry must be neutralised to pH 10.5–11. This typically requires limestone or lime addition in a multi-stage neutralisation circuit, consuming significant reagent mass and adding cost.

For very high-sulfide concentrations (> ~6% S), autoclaves operate in **controlled acid-balance mode**: the acid generated by one pass through the autoclave is recycled to partially dissolve the carbonate gangue minerals in the incoming feed, reducing external lime consumption.

## Oxygen Supply

High-purity oxygen (95%+) from a cryogenic air separation plant (ASP) is typically the single largest capital cost item in a POX facility. Oxygen consumption is approximately 0.8–1.2 tonnes O₂ per tonne of sulfide sulfur oxidised, and modern autoclaves operate with oxygen utilisation efficiencies of 85–95%.

## Temperature Regimes and Product Quality

| Mode | Temperature | Dominant Fe product | Dominant As product | Cyanidation |
|---|---|---|---|---|
| Low-temperature POX | 190–205 °C | Goethite / jarosite | Amorphous ferric arsenate | Good, ~90–95% Au recovery |
| High-temperature POX | 220–235 °C | Hematite | Crystalline scorodite | Excellent, ~95–99% Au recovery |

High-temperature POX gives better gold recovery because hematite forms discrete, open-porous particles that expose liberated gold surface area to cyanide, whereas jarosite can occlude gold. The trade-off is higher operating cost (energy, oxygen, exotic metallurgy for autoclave internals).

## Industrial Installations

Major POX plants operating globally (2024):
- **Goldstrike (Nevada, USA)** — Barrick Gold; two large horizontal autoclaves treating Carlin-type ore since 1999; ~1.5 Mtpa feed capacity
- **Pueblo Viejo (Dominican Republic)** — Barrick/Newmont JV; high-temperature POX at 230 °C; ~25,000 t/d feed
- **Olimpiada (Russia)** — Polyus; largest POX plant in the world at ~10 Mtpa; double refractory ore
- **Veladero (Argentina)** — Barrick; high-altitude installation at >4000 m ASL
- **Fosterville (Australia)** — Agnico Eagle; recent expansion to treat sulfarsenide-hosted gold

## Advantages over Roasting

- No SO₂ or As₂O₃ gas emissions (arsenic stays in solution/slurry)
- Arsenic product (scorodite) is suitable for tailings disposal without dedicated off-gas treatment
- Better gold recovery from highly refractory ores
- Shorter treatment time than biooxidation
- Easier to control and automate

## Limitations

- Very high capital cost (USD 200–500 million for a large plant)
- High maintenance cost (titanium liners, oxygen plant, acid-resistant pumps)
- Not suitable for high-carbonate ores (excessive acid consumption)
- Not suitable for very high-pyrrhotite ores (flash oxidation, temperature runaway risk)
MD,
            ],

            // ── 7 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['biooxidation', 'BIOX', 'acidithiobacillus']),
                'title'       => 'Bacterial Oxidation (BIOX): Microbiology, Process Engineering, and Industrial Scale-Up',
                'summary'     => 'Biooxidation uses iron- and sulfur-oxidising bacteria to catalyse the atmospheric oxidation of sulfide minerals at ambient pressure and 35–50 °C. It offers lower capital cost and eliminates SO₂ emissions relative to roasting, and has been proven at commercial scale for over 30 years.',
                'content'     => <<<'MD'
## Microbiological Basis

Biooxidation exploits naturally occurring chemolithotrophic microorganisms that obtain energy by oxidising reduced sulfur and iron compounds. The dominant organisms in industrial BIOX plants are:

**Acidithiobacillus ferrooxidans** (formerly *Thiobacillus ferrooxidans*): gram-negative, rod-shaped bacterium, optimal pH 1.5–2.5, optimal temperature 28–35 °C. Oxidises Fe²⁺ → Fe³⁺ and S²⁻ → SO₄²⁻.

**Acidithiobacillus thiooxidans:** Specialist sulfur oxidiser, tolerates pH < 1, important for thiosulfate and polysulfide oxidation.

**Sulfobacillus thermosulfidooxidans** and **Ferroplasma acidiphilum:** Moderately thermophilic organisms (45–60 °C) used in higher-temperature BIOX variants such as the BacTech/Mintek BACOX process; provide faster kinetics and treat more refractory sulfide matrices.

**Archaea (Sulfolobus metallicus, Metallosphaera sedula):** Extreme thermophiles (60–80 °C) used in experimental and some commercial heap biooxidation; oxidise refractory minerals that resist mesophilic bacteria.

## Reaction Chemistry

Bacteria act as catalysts: they regenerate the Fe³⁺ that chemically attacks sulfide minerals, and they oxidise elemental sulfur intermediates that would otherwise passivate mineral surfaces.

**Direct attack (enzymatic):**
FeS₂ + bacteria → FeS₂·[bacterial exopolysaccharide] → Fe²⁺ + S₂O₃²⁻ + …

**Indirect attack (chemical, Fe³⁺-mediated, bacteria regenerate Fe³⁺):**
FeS₂ + 14 Fe³⁺ + 8 H₂O → 15 Fe²⁺ + 2 SO₄²⁻ + 16 H⁺
4 Fe²⁺ + O₂ + 4 H⁺ → 4 Fe³⁺ + 2 H₂O (bacterially catalysed, 10⁵× faster than abiotic)

The net stoichiometry of bacterial pyrite oxidation is identical to POX and abiotic roasting; only the kinetics and conditions differ.

## Industrial BIOX Process (Outotec/Metso Outotec)

The proprietary BIOX® process, developed by Gencor in the 1980s and now licensed by Metso Outotec, uses a continuous stirred-tank reactor (CSTR) cascade:

1. **Feed preparation:** Ore ground to 80% < 53 µm to expose sulfide surfaces; slurry diluted to 10–20% solids.
2. **Primary tanks (3 tanks in parallel):** Large 1000–2500 m³ CSTRs inoculated with active culture; vigorous air sparging (0.1–0.15 vvm); cooling coils maintain 35–40 °C; pH 1.4–1.8 controlled with H₂SO₄.
3. **Secondary tanks (2–3 tanks in series):** Polishing oxidation; residence time extends to achieve target sulfide oxidation (typically 70–90%).
4. **Counter-current wash:** Removes residual acid and dissolved metals before the critical neutralisation step.
5. **Neutralisation:** Lime addition to pH 10.5–11 for subsequent cyanidation.

Typical gold recovery improvement over direct cyanidation: from 10–40% to 90–97% for arsenopyrite-dominant ores.

## Commercial Installations

| Mine | Country | Ore type | Capacity | Operator |
|---|---|---|---|---|
| Fairview | South Africa | Arsenopyrite | 35 t/d concentrate | Pan African Resources |
| Sansu/Obuasi | Ghana | Arsenopyrite + carbon | 1000 t/d concentrate | Anglogold Ashanti |
| Fosterville (hist.) | Australia | Arsenopyrite | 350 t/d ore | Kirkland Lake (pre-POX) |
| Laizhou | China | Sulfidic ore | 500 t/d | Zhaojin Mining |
| Suzdal | Kazakhstan | Arsenopyrite | 300 t/d concentrate | Polymetal |

## Advantages and Limitations

**Advantages:**
- Lower capital cost than POX (~40–60% of equivalent POX plant)
- No SO₂ or As₂O₃ gas-phase emissions
- Modular and scalable
- Self-sustaining once inoculated; low energy input

**Limitations:**
- Slow: residence time 4–6 days (vs. 1–2 h for POX)
- Sensitive to process upsets, toxic metals (Ag, Hg, Bi), and temperature excursions
- Treats concentrates or fine-grained ore; not suitable for coarse-grained material
- Produces large volumes of ferric sulfate/arsenic-bearing effluent requiring neutralisation and disposal
- Arsenic product (amorphous ferric arsenate) less stable than POX scorodite
MD,
            ],

            // ── 8 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['roasting', 'calcination', 'SO2', 'pyrite oxidation']),
                'title'       => 'Roasting and Calcination: Pyrometallurgical Pre-Treatment for Refractory Gold',
                'summary'     => 'Roasting oxidises sulfide minerals at 450–750 °C in air or oxygen-enriched atmosphere. It is the oldest refractory gold pre-treatment technology, still widely used where calcine quality is high and emissions controls are in place, but faces increasing regulatory pressure due to SO₂ and As₂O₃ off-gas.',
                'content'     => <<<'MD'
## Principle and History

Roasting — heating sulfide-bearing material in an oxidising atmosphere — has been used to treat refractory gold ores for over a century, predating both pressure oxidation and biooxidation. The process thermally decomposes sulfide minerals, releasing gold from the sulfide lattice and converting sulfur to SO₂ (which can be captured as sulfuric acid or neutralised with lime) and arsenic to volatile As₂O₃ (which must be captured in electrostatic precipitators and bag filters).

## Reaction Sequences

The oxidation of pyrite proceeds through an intermediate pyrrhotite stage:

**Low-temperature (400–500 °C), limited O₂:**
FeS₂ → FeS (pyrrhotite) + S° → FeS + S° (S° evaporates or further oxidises)

**Higher-temperature (500–700 °C), excess O₂:**
FeS₂ + O₂ → FeS + SO₂ (partial)
4 FeS + 7 O₂ → 2 Fe₂O₃ + 4 SO₂ (complete)
4 FeS₂ + 11 O₂ → 2 Fe₂O₃ + 8 SO₂ (overall)

Arsenopyrite oxidation:
4 FeAsS + 9 O₂ → 4 FeO + 4 As₂O₃(g) + 4 SO₂

At > 600 °C without arsenic capture:
2 As₂O₃ + O₂ → 2 As₂O₅ (if cooled slowly) — or volatilises as As₂O₃ fume

## Furnace Types

**Multiple-hearth furnace (MHF):** Consists of 5–14 horizontal hearths stacked vertically; ore is raked progressively downward by rotating rabble arms. Provides long residence time and good temperature control in each zone; suited for ore concentrates. Capital-intensive; mechanical complexity of the rabble arm drive at high temperature.

**Fluid-bed roaster (FBR):** Finely ground feed is fluidised in an upward air stream within a cylindrical vessel; temperature uniformity is excellent; throughput per unit volume is 5–10× higher than MHF. Standard technology for new plants; used at Barrick's Goldstrike since 2000 and at Newmont's Nevada operations.

**Rotary kiln:** Used for coarser feeds and pre-drying; less commonly used for oxidative roasting of gold concentrates due to poor temperature control and over-roasting risk.

## Critical Process Parameters

**Temperature control:** The gold recovery window is narrow. Below ~480 °C, sulfide oxidation is incomplete and gold remains locked. Above ~650–700 °C, sintering and scorodite armoring occlude liberated gold — the "over-roasting" penalty. The optimal window for arsenopyrite-bearing concentrates is 550–620 °C.

**SO₂ capture:** SO₂ concentrations in off-gas from a fluid-bed roaster typically reach 5–15%. Acid plant conversion (> 99.7% SO₂ → H₂SO₄) is mandatory in most jurisdictions. Weak-acid sulfuric acid is a by-product sold to fertiliser producers.

**Arsenic capture:** As₂O₃ volatilises at > 450 °C; it is captured in a series of electrostatic precipitators (ESPs) and baghouses. The collected As₂O₃ (arsenolite/claudetite) is a valuable by-product (used in glass, agrochemicals, semiconductors) or must be stabilised as ferric arsenate and disposed of in a lined facility.

## Calcine Quality and Gold Recovery

A well-operated roast produces a porous, hematitic calcine with gold liberated to the grain surface. Gold recoveries of 92–98% by subsequent cyanidation are routine for properly roasted arsenopyrite concentrates.

Poor calcine quality from over-roasting (sintered, low porosity) or under-roasting (residual sulfides) reduces recovery to 70–85%. Online calcine monitoring using XRD (X-ray diffraction) or inductively coupled plasma–optical emission spectrometry (ICP-OES) of calcine digest is used at modern plants to control the roast.

## Environmental Considerations

Modern roasters must comply with:
- SO₂ emission limits (EU: < 500 mg/Nm³ after acid plant)
- Particulate/heavy metal emission limits (EU: < 1 mg/Nm³ total arsenic in stack)
- Arsenic waste disposal regulations (EU Waste Framework Directive, US RCRA)

These requirements add significant capital and operating cost. Several older roaster operations have been converted to POX or BIOX as regulatory requirements tightened, particularly in the EU and Australia.
MD,
            ],

            // ── 9 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['ultra-fine grinding', 'UFG', 'IsaMill', 'mechanical liberation']),
                'title'       => 'Ultra-Fine Grinding (UFG) as Pre-Treatment: Principles, Equipment, and Application',
                'summary'     => 'Ultra-fine grinding reduces ore to 80% passing 10–20 µm, physically exposing gold locked in fine-grained sulfide matrices without chemical oxidation. When combined with intensive cyanidation, UFG can achieve competitive recoveries for mildly to moderately refractory ores at lower capital cost than oxidative pre-treatment.',
                'content'     => <<<'MD'
## Principle

Ultra-fine grinding (UFG) relies on a simple idea: if gold particles are encapsulated within sulfide grains, grind the sulfide grains small enough that every gold particle is either liberated into a free state or exposed at a grain surface accessible to cyanide. No chemistry is required — only mechanical energy.

In practice, UFG is viable as a standalone pre-treatment when:
- Gold particles are > ~1–2 µm (physical liberation is possible at P₈₀ = 10–20 µm)
- Sulfide grain size is < ~50–100 µm (so that P₈₀ = 20 µm achieves most liberation)
- Gold is not primarily in solid solution (solid-solution gold cannot be liberated mechanically)

UFG is not effective for truly refractory ores where gold is in solid solution or nanoparticles < 1 µm — these require chemical destruction of the sulfide lattice.

## Equipment: IsaMill and Vertimill

**IsaMill (Glencore Technology):** A horizontal stirred media mill using inert ceramic or sand grinding media (0.5–3 mm). Feed slurry is pumped through a series of rotating discs that agitate the media. Specific energy input: 20–100 kWh/t to achieve P₈₀ < 20 µm. Key advantage: sharp product size distribution (low proportion of unground coarse particles) and ability to use inert media, avoiding iron contamination that inhibits cyanidation.

**M10000 IsaMill:** The largest unit, with a 10 000 L chamber, 3 MW installed power; used at Prominent Hill (OZ Minerals) and McArthur River (Glencore) for base metals, and increasingly for refractory gold concentrates.

**Vertimill (Metso Outotec):** Vertical stirred mill with screw impeller; well-established for P₈₀ targets of 20–75 µm. Less effective below 15 µm due to media size limitations, but lower capital cost than IsaMill for intermediate-fine targets.

**High-Pressure Grinding Rolls (HPGR):** Not technically UFG, but increasingly used upstream of ball/IsaMills to reduce specific energy for the fine-grinding stage. HPGR creates micro-cracking in sulfide minerals that weakens the host and improves downstream grinding efficiency.

## Liberation Analysis

Liberation analysis by **QEMSCAN** (Quantitative Evaluation of Minerals by Scanning Electron Microscopy) or **MLA** (Mineral Liberation Analyser) measures the degree of gold liberation as a function of grind size. The output — liberation curves showing free gold vs. P₈₀ — determines the grind target for maximum recovery.

For typical refractory concentrates with gold grain sizes of 5–30 µm:
- At P₈₀ = 75 µm (standard ball mill): 40–60% gold liberation
- At P₈₀ = 25 µm (IsaMill): 75–90% gold liberation
- At P₈₀ = 10 µm (IsaMill high-intensity): 90–98% gold liberation

## Intensive Cyanidation after UFG

After UFG, the concentrate is typically leached in a dedicated intensive cyanidation reactor (e.g., InLine Leach Reactor — ILR) at high cyanide concentrations (5–25 g/L CN⁻) and elevated dissolved oxygen. The short retention time and high reagent concentrations exploit the fresh grain surfaces created by UFG before re-passivation occurs.

Gold recovery sequence for UFG + intensive cyanidation:
- Mildly refractory: 85–93% Au recovery
- Moderately refractory: 78–88% Au recovery
- Highly refractory: 55–70% Au recovery (insufficient without chemical pre-treatment)

## Economic Case

UFG capital cost for a 50 t/h concentrate stream: ~USD 15–30 million (vs. USD 80–200 million for a BIOX plant of equivalent capacity). For mildly refractory ores, the lower capital cost often makes UFG the preferred option, accepting slightly lower gold recovery in exchange for substantially less capital risk. Several juniors have selected UFG specifically to reduce pre-production capital.
MD,
            ],

            // ── 10 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['thiosulfate leaching', 'alternative lixiviant', 'non-cyanide']),
                'title'       => 'Thiosulfate Leaching: Chemistry, Advantages, and Commercial Implementation',
                'summary'     => 'Ammoniacal thiosulfate is the only non-cyanide gold lixiviant proven at commercial scale. It is particularly effective for double refractory ores and oxidised ores containing silver, mercury, or copper that interfere with cyanide, but high reagent consumption has historically limited its economics.',
                'content'     => <<<'MD'
## Chemistry of Gold Dissolution in Thiosulfate

Gold dissolves in ammoniacal thiosulfate through an oxidative complexation reaction catalysed by copper(II):

**4 Au + 8 S₂O₃²⁻ + O₂ + 2 H₂O → 4 [Au(S₂O₃)₂]³⁻ + 4 OH⁻** (net)

The active oxidant is Cu(NH₃)₄²⁺ (tetraamminecopper(II)), and the reaction mechanism involves:
1. Cu(II) oxidises Au⁰ to Au⁺
2. Au⁺ is stabilised by two thiosulfate ligands: Au⁺ + 2 S₂O₃²⁻ → [Au(S₂O₃)₂]³⁻
3. Cu(I) generated in step 1 is re-oxidised to Cu(II) by dissolved oxygen

The rate-limiting step under most conditions is step 3 (Cu(I) re-oxidation), making dissolved oxygen concentration a critical control variable.

**Standard leach conditions (ammoniacal thiosulfate):**
- Thiosulfate concentration: 10–50 g/L Na₂S₂O₃
- Ammonia: 0.2–1.0 M NH₃ (stabilises copper and prevents Fe³⁺ interference)
- Copper: 20–200 mg/L Cu(II)
- pH: 9.5–10.5
- Temperature: 25–45 °C
- Dissolved O₂: 5–8 mg/L

## Advantages over Cyanide for Refractory Ores

**1. No preg-robbing by natural carbon:** [Au(S₂O₃)₂]³⁻ has a lower affinity for carbonaceous matter than [Au(CN)₂]⁻. Some experimental and commercial data show that naturally carbonaceous ores can be treated with thiosulfate without carbon passivation steps, simplifying the process.

**2. Not poisoned by mercury, arsenic, or antimony:** Cyanide consumption increases dramatically in the presence of these elements (forming SCN⁻ and other thiocyanate species, and chelating As/Sb). Thiosulfate is more selective.

**3. Lower toxicity:** Thiosulfate is not classified as a hazardous substance in most jurisdictions (unlike cyanide, which requires ICMI compliance, cyanide code management plans, etc.). This simplifies community engagement and permitting in environmentally sensitive regions.

**4. Gold recovery from telluride ores and some calaverite-bearing ores:**
Telluride minerals resist cyanide without prior oxidation; thiosulfate leaches them more readily under mild conditions.

## Thiosulfate Decomposition — The Core Challenge

Thiosulfate is thermodynamically unstable in the presence of oxygen and copper:
**2 S₂O₃²⁻ + O₂ → S₄O₆²⁻ + 2 OH⁻** (tetrathionate formation)
Tetrathionate further disproportionates to polythionates, sulfate, and elemental sulfur.

The result: thiosulfate consumption rates of 1–10 kg/t ore are common, compared to 0.1–0.5 kg/t for cyanide. At USD 250–400/t for sodium thiosulfate, this is economically prohibitive for low-grade ores.

Research programmes at Barrick Gold, CSIRO, and multiple universities have focused on:
- Ion exchange resin recovery (resins adsorb [Au(S₂O₃)₂]³⁻ strongly but require careful elution)
- Electrochemical regeneration of thiosulfate from tetrathionate
- Copper sulphate catalyst optimisation
- Reducing dissolved oxygen to minimise thiosulfate oxidation while maintaining gold leach rate

## Commercial Implementation: Goldstrike (Barrick, Nevada)

Barrick Gold commenced commercial thiosulfate leaching at Goldstrike in 2014, treating the double refractory (sulfidic + carbonaceous) oxide ore stockpile that had accumulated because direct cyanidation was uneconomic. The plant processes ~9000 t/d of POX-calcine using an ammoniacal thiosulfate system with resin-in-leach (RIL) recovery, where strong-base ion-exchange resin replaces activated carbon.

The Goldstrike implementation proved the concept at scale, achieving > 85% gold recovery on a feed that yielded < 30% by direct cyanidation, but thiosulfate consumption management remains the central operational challenge.
MD,
            ],

            // ── 11 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['Carlin-type', 'Nevada', 'sediment-hosted gold']),
                'title'       => 'Carlin-Type Gold Deposits: Geology, Mineralogy, and Why They Are Inherently Refractory',
                'summary'     => 'Carlin-type gold deposits (CTGDs) are the largest class of gold deposits in North America, concentrated in the Nevada Basin and Range Province. Their gold is uniquely refractory — hosted in arsenian pyrite and carbonaceous sediment simultaneously — because of the specific hydrothermal and diagenetic processes that formed them.',
                'content'     => <<<'MD'
## Definition and Setting

Carlin-type gold deposits (CTGDs) were first recognised at the Carlin Mine, Elko County, Nevada, in 1961 by Newmont Mining geologists. They are characterised by:

- Sediment-hosted (not intrusion-proximal) emplacement in Paleozoic carbonate and siliciclastic sequences
- Invisible (submicroscopic) gold in arsenian pyrite; no visible gold, no quartz-sulfide veins
- Low ore grade (1–3 g/t Au) but very large tonnage (> 100 Mt at many deposits)
- Strong spatial association with high-angle faults and fold hinges that acted as fluid conduits
- Intimate association with carbonaceous (organic carbon-bearing) host rocks

The two main mineral belts — the **Carlin Trend** (Betze-Post, Meikle, Twin Creeks) and the **Battle Mountain–Eureka Trend** (Pipeline, Cortez, Goldrush) — together contain > 200 Moz of gold, making Nevada the third-largest gold-producing jurisdiction in the world.

## Ore Mineralogy

**Arsenian pyrite:** The gold-hosting pyrite in CTGDs is distinct from sedimentary diagenetic pyrite. Two pyrite generations are universally present:
- *Py1 (diagenetic):* Fine-grained, porous, low As; formed during early burial. Contains minor gold.
- *Py2 (hydrothermal):* Euhedral, growth-zoned, As-rich rims; precipitated during the Eocene (40–35 Ma) hydrothermal mineralisation event. This is the primary gold host. LA-ICP-MS mapping reveals concentric Au-rich zones in Py2 rims corresponding to punctuated ore-fluid pulses.

As concentrations in Py2 reach 1–5 wt%; Au concentrations 10–3000 ppm. Per the Reich et al. (2005) solubility limit, most gold plots at or above the solubility line — meaning it is present as both solid-solution Au⁺ and nanoparticles.

**Carbonaceous matter (CM):** Paleozoic organic shales and limestones in the CTGD host environment contain 0.1–5 wt% organic carbon. During hydrothermal fluid flow, some of this kerogen was remobilised and re-deposited as structureless bituminous carbon in permeable zones adjacent to the gold ore. This CM is the primary preg-robbing agent in CTGD processing.

**Clay minerals and Hg-Sb-As anomalies:** Realgar (AsS), orpiment (As₂S₃), stibnite (Sb₂S₃), cinnabar (HgS), and hydrothermal clay minerals (illite, kaolinite) are pathfinder minerals for CTGDs and also interfere with cyanide processing by consuming reagent.

## The Carlin Hydrothermal Event

Geochronological studies (Ar/Ar on illite, U-Pb on apatite) date the CTGD mineralisation to the Eocene (40–36 Ma), coincident with the onset of Basin and Range extension. The ore fluids were low-temperature (150–250 °C), low-salinity (2–5 wt% NaCl eq.), and meteoric-dominated — a fluid very different from the high-temperature, magmatic-hydrothermal fluids that form epithermal and porphyry gold deposits.

Gold was transported as Au(HS)₂⁻ (gold bisulfide complex) at pH 5–6, or possibly as AuHS⁰. Gold precipitation was triggered by:
- Phase separation (boiling) producing CO₂ exsolution
- Sulfidation of Fe-rich host rocks consuming H₂S
- Cooling and mixing with cooler groundwater

The very low temperatures of CTGD formation (< 250 °C) explain why gold is submicroscopic: at these conditions, gold nucleation rates are low and crystal growth is slow, favouring solid-solution incorporation over discrete particle growth.

## Processing Challenges Specific to CTGDs

- **Double refractoriness:** Both arsenian pyrite (physical/chemical lock) and carbonaceous matter (preg-robbing) must be addressed simultaneously.
- **Low Au grade** (< 3 g/t) limits pre-treatment options to those with low operating cost: roasting or BIOX (not POX, which is too capital-intensive for most CTGD operations).
- **High Hg, As, Sb content** creates environmental compliance obligations; Nevada roasters operate dedicated Hg retort systems.
- **High carbonate gangue** (CTGDs typically hosted in limestone): acid-generating pre-treatment processes (POX) require extreme neutralisation reagent consumption.

Barrick's Goldstrike and Newmont's Nevada Operations are the global benchmarks for CTGD processing technology.
MD,
            ],

            // ── 12 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['synchrotron', 'LA-ICP-MS', 'SIMS', 'nano-SIMS', 'TEM']),
                'title'       => 'Advanced Characterisation of Refractory Gold: Synchrotron, LA-ICP-MS, SIMS, and TEM',
                'summary'     => 'The past 20 years have seen a revolution in refractory gold characterisation, driven by the application of synchrotron X-ray techniques, laser ablation ICP-MS mapping, SIMS, and aberration-corrected TEM. These tools reveal gold siting at the nanometre to atomic scale, directly informing process selection.',
                'content'     => <<<'MD'
## Why Advanced Characterisation Matters

The single most important metallurgical decision for a refractory gold project — which pre-treatment technology to select — depends on knowing whether gold is:
1. In physical grains accessible by grinding
2. As nanoparticles (Au⁰) within sulfide hosts
3. In atomic solid solution (Au⁺) within the sulfide lattice

Standard optical microscopy and SEM cannot distinguish between options 2 and 3. Advanced techniques are required.

## LA-ICP-MS Elemental Mapping

**Laser Ablation Inductively Coupled Plasma Mass Spectrometry (LA-ICP-MS)** uses a pulsed UV laser (193 nm ArF excimer) to ablate material from a polished mineral surface in a rastered pattern; the ablated aerosol is transported to an ICP-MS instrument, providing real-time multi-element concentration at each ablation point.

**Capabilities:**
- Spatial resolution: 2–15 µm (depending on laser spot size)
- Detection limits: 0.01–0.1 ppm for Au, As, Ag, Sb, Bi, Te
- 2D elemental maps of entire pyrite or arsenopyrite grains
- Multi-element correlations (Au vs. As, Au vs. Ni/Co) to distinguish pyrite generations

**Key outputs for refractory gold:**
- Maps of Au distribution showing concentric zoning — high-Au zones correspond to hydrothermal overgrowths (Py2) vs. diagenetic cores (Py1)
- Au–As scatter plots compared against the Reich et al. (2005) solubility limit line: points above the line → Au nanoparticles likely
- Identification of "nugget effects" — statistical spikes in time-resolved LA-ICP-MS signals indicating discrete Au particles passing through the ablation pit

## Synchrotron X-Ray Techniques

**Synchrotron X-ray fluorescence (SXRF) mapping:**
Synchrotron beamlines (e.g., at the Australian Synchrotron, Stanford SSRL, European ESRF) produce X-ray beams 10⁶–10⁹ times more intense than laboratory X-ray sources. μ-SXRF mapping at 1–50 µm spatial resolution provides Au, As, Fe, S elemental maps with < 0.1 ppm detection limits — sufficient to map gold in solid solution within single pyrite grains.

**X-ray Absorption Near-Edge Structure (XANES):**
XANES spectra of the Au L₃ absorption edge (11.919 keV) distinguish Au⁰ (metallic, reference Au foil) from Au⁺ (ionic, reference Au(I) compounds). By measuring the Au L₃ XANES on gold-rich pyrite, researchers can directly determine the oxidation state of gold — and therefore whether it is in solid solution (Au⁺) or nanoparticles (Au⁰).

This technique, pioneered by Simon et al. (1999) using synchrotron beamlines, provided the first direct proof of solid-solution gold in arsenian pyrite from Carlin-type deposits.

**Micro-X-ray diffraction (µ-XRD):**
Identifies micro-scale mineral assemblages around gold-bearing zones, revealing whether gold is associated with specific mineralogical micro-environments (e.g., gold-rich pyrite next to carbonate replacement zones).

## SIMS and NanoSIMS

**Secondary Ion Mass Spectrometry (SIMS):** A primary ion beam (Cs⁺ or O⁻) sputters secondary ions from the sample surface; the secondary ions are mass-selected and detected. SIMS provides:
- Detection limits < 0.1 ppm for Au with ~15 µm spot size
- Isotopic measurements on gold-bearing phases
- Depth profiling (concentration as a function of depth into a grain)

**NanoSIMS:** Reduces spatial resolution to 50–100 nm using a more tightly focused primary beam, enabling gold imaging at the grain-boundary scale. Used to map gold in micro-fractures and defect zones within sulfide grains that are unresolvable by conventional SIMS or LA-ICP-MS.

## TEM/STEM with EDS

**Transmission Electron Microscopy (TEM)** and its scanning variant (STEM) achieve atomic-scale (< 0.2 nm) imaging. For refractory gold:
- Thin foils extracted from gold-rich pyrite by Focused Ion Beam (FIB) milling are examined in TEM
- Direct imaging of Au nanoparticles (1–20 nm) in pyrite lattice defects and grain boundaries
- Selected Area Electron Diffraction (SAED) confirms crystallinity (Au⁰ nanoparticles are FCC-structured)
- Energy Dispersive X-ray Spectrometry (EDS) on individual nanoparticles provides elemental composition

Key TEM studies (Fleet & Mumin, 1997; Palenik et al., 2004; Deditius et al., 2011) established that both solid-solution and nanoparticulate gold coexist within individual pyrite grains, and that the proportion varies systematically with the local As concentration and growth conditions.

## Integrated Workflow

Modern refractory gold characterisation programs combine:
1. LA-ICP-MS mapping on 20–50 pyrite/arsenopyrite grains across ore types
2. Au–As solubility limit analysis to predict solid-solution vs. nanoparticle fractions
3. Select SXRF/XANES measurements to confirm Au oxidation state for representative samples
4. FIB-TEM on 2–5 samples from the most gold-rich zones for direct nanoparticle imaging

This multi-scale, multi-technique approach provides definitive gold deportment data that cannot be obtained by any single method.
MD,
            ],

            // ── 13 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['refractoriness index', 'locking coefficient', 'flotation']),
                'title'       => 'Quantifying Refractoriness: The Refractoriness Index, Preg-Robbing Index, and Flotation Pre-Concentration',
                'summary'     => 'Before selecting a pre-treatment technology, engineers quantify the degree of refractoriness using standardised indices derived from bottle roll tests, preg-robbing tests, and flotation response data. These indices drive process selection, plant sizing, and financial modelling.',
                'content'     => <<<'MD'
## The Refractoriness Index (RI)

The Refractoriness Index is defined as the fraction of gold that is not recovered by direct cyanidation under optimal conditions:

**RI = 1 − (Au recovery by direct CN⁻ / Au recovery by complete dissolution)**

An RI of 0 = free-milling; RI = 1 = completely refractory. In practice:

| RI range | Classification | Typical pre-treatment |
|---|---|---|
| 0–0.10 | Free-milling | None |
| 0.10–0.30 | Mildly refractory | UFG or mild oxidation |
| 0.30–0.60 | Moderately refractory | BIOX or roasting |
| > 0.60 | Highly refractory | POX or high-temperature roasting |

RI is always measured on the ore at a defined grind size (commonly P₈₀ = 75 µm); it is not a fixed material property — it decreases as grind size decreases (more liberation at finer grind). Reporting grind size alongside RI is mandatory for meaningful comparison.

## The Preg-Robbing Index (PRI)

**PRI = (Au adsorbed by ore carbon from synthetic pregnant solution) / (Au in synthetic pregnant solution)**

Measured by contacting the crushed ore with a gold-cyanide solution of known concentration for a fixed period, then measuring residual gold in solution. PRI > 0.10 (10% gold loss to natural carbon) triggers investigation of carbon passivation or alternative lixiviant selection.

A complementary test — the **Activated Carbon Competition Test (ACCT)** — runs parallel leach tests with and without competing activated carbon addition. High carbon competition recovery indicates strong preg-robbing that can be mitigated by adding excess activated carbon.

## The Locking Coefficient (LC)

The Locking Coefficient quantifies the proportion of gold locked within sulfide grains versus free in the pulp:

**LC = (Au in sulfide-locked fraction) / (Total Au)**

Measured from diagnostic leaching or from QEMSCAN/MLA image analysis of polished sections at the leach grind size. High LC (> 0.5) indicates chemical pre-treatment is essential; low LC (< 0.2) suggests UFG or flotation pre-concentration can address the problem.

## Flotation as Pre-Concentration

Before applying expensive chemical pre-treatment to whole ore, most operations first use **flotation** to produce a sulfide concentrate that contains the bulk of the refractory gold in a small mass fraction (typically 10–30% of the feed mass, containing 60–90% of the gold).

The flotation concentrate — rather than the whole ore — is then pre-treated by POX, BIOX, or roasting. This dramatically reduces pre-treatment plant size and cost.

**Typical flotation conditions for refractory gold sulfides:**
- Collector: potassium amyl xanthate (PAX) or sodium isobutyl xanthate (SIBX) at 20–100 g/t
- Frother: MIBC (methyl isobutyl carbinol) or Dowfroth at 5–30 g/t
- pH: 7.5–9.5 (controlled with lime)
- Grind: P₈₀ = 53–106 µm (coarser than final leach grind to maximise concentrate grade)

**Concentrate upgrading:**
Cleaner flotation stages remove entrained silicate/carbonate gangue. Target concentrate grade: > 30 g/t Au, > 15% S (sufficient sulfide content to generate adequate heat in autoclave/roaster). Final concentrates may contain 100–500 g/t Au for high-grade deposits.

## Economic Impact of RI and PRI on Project Economics

A 10-percentage-point difference in gold recovery (e.g., 88% vs. 78%) translates directly to revenue. At 250,000 oz/year production and USD 2000/oz gold price, a 10% recovery improvement is worth USD 50 million/year in additional revenue — easily justifying the capital investment in pre-treatment even at USD 100–200 million capital cost.

This arithmetic explains why detailed refractoriness characterisation during the feasibility study phase is one of the highest-return investments in any refractory gold project's development.
MD,
            ],

            // ── 14 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['glycine leaching', 'glycinate', 'non-cyanide gold']),
                'title'       => 'Glycine Leaching: Emerging Non-Cyanide Lixiviant for Refractory and Oxide Gold Ores',
                'summary'     => 'Glycine (aminoacetic acid) leaches gold as gold-glycinate complexes under mildly alkaline, oxidising conditions. Its low toxicity, biodegradability, and effectiveness in selective gold leaching from oxide and mildly refractory ores has attracted significant research and pilot plant attention since 2014.',
                'content'     => <<<'MD'
## Discovery and Background

Glycine leaching for gold was systematically investigated and patented by the Western Australian School of Mines (WASM) and Curtin University group (Eksteen, Oraby, and colleagues) beginning around 2014, building on earlier observations of amino acid–metal complex chemistry. The key publication (Oraby & Eksteen, 2014, *Hydrometallurgy*) demonstrated that gold dissolves in alkaline glycine solutions at commercially relevant rates under ambient conditions.

## Chemistry

Gold dissolves as the gold(I)-glycinate complex:

**2 Au + 4 Gly + ½ O₂ + H₂O → 2 [Au(Gly)₂]⁻ + 2 OH⁻**

where Gly = glycine (H₂N–CH₂–COOH, pKa = 2.35 and 9.60).

The gold-glycinate complex [Au(Gly)₂]⁻ is stable at pH 9–11, where glycine exists as the zwitterion/anion H₂N–CH₂–COO⁻. The complex is less stable than [Au(CN)₂]⁻ (formation constant log K ≈ 18 for glycinate vs. ≈ 38 for cyanide), which means:
- Lower leaching kinetics (hours to days vs. hours for cyanide under optimal conditions)
- Not adsorbed as strongly onto activated carbon — requiring alternative recovery (e.g., electrowinning or IX resin)

## Selectivity

A critical advantage: glycine does not dissolve base metals (Cu, Zn, Fe, Ni) significantly under the mild alkaline conditions used for gold leaching. This selectivity is advantageous for:
- **Copper-gold ores:** Cyanide consumes enormous quantities leaching copper before reaching gold. Glycine leaves copper in the residue and dissolves gold selectively.
- **High-arsenic or antimony ores:** Arsenic and antimony do not form stable glycinate complexes, so they do not poison the lixiviant.
- **Oxide gold ores with complex mineralogy:** Reduces reagent consumption and simplifies tailings chemistry.

## Refractory Gold Application

For sulfide-locked gold, glycine alone (at atmospheric pressure) is insufficient — it cannot dissolve the sulfide host. However, glycine shows promise in combination with pre-treatment:

**Glycine + H₂O₂ (glycine-peroxide system):** H₂O₂ provides the oxidant needed to both oxidise sulfide surfaces (partially liberating gold) and oxidise Au⁰ to Au⁺ for glycinate complexation. Lab results show 70–85% gold recovery from mildly refractory ores in 24–48 h — competitive with cyanidation of similar material.

**Post-POX glycine leaching:** After pressure oxidation destroys the sulfide host, the oxidised calcine is receptive to glycine leaching in place of cyanidation. This eliminates cyanide from the circuit entirely — significant for mines in jurisdictions with cyanide restrictions or strong community opposition to cyanide use.

## Current Development Status (2024–2025)

Glycine leaching remains at pilot/demonstration scale for most applications. Key developments:
- **CuDECO Ltd (Australia):** Piloted glycine heap leaching for copper-gold; demonstrated selective Cu recovery separate from Au.
- **Curtin University/CSIRO collaboration:** Continuous development of glycine-cyanide mixed-lixiviant systems (adding 50–200 ppm glycine to cyanide circuits reduces cyanide consumption by 20–40% by forming mixed complexes that resist thiocyanate formation from sulfide interference).
- **Glycine recovery and recycle:** Economic viability depends on recovering > 90% of glycine from leach residues; ceramic nanofiltration membranes achieving 92–96% recovery demonstrated at lab scale.

## Limitations

- Kinetics ~5–10× slower than cyanide at equivalent gold tenor — requires longer retention time or elevated temperature (50–60 °C improves rates significantly)
- Reagent cost: glycine ~USD 700–1200/t vs. cyanide ~USD 1500–2500/t, but higher consumption partially offsets the cost advantage
- Recovery circuit: [Au(Gly)₂]⁻ is not efficiently loaded onto standard activated carbon; requires IX resin or direct electrowinning
- Not yet commercially proven for high-throughput whole-ore leach applications
MD,
            ],

            // ── 15 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['mechanochemistry', 'mechanochemical activation', 'grinding activation']),
                'title'       => 'Mechanochemical Activation of Refractory Gold Ores: Science and Emerging Applications',
                'summary'     => 'Mechanochemical activation uses high-energy milling to introduce lattice defects, amorphise sulfide minerals, and partially oxidise sulfide surfaces — reducing their refractoriness without the energy penalty of full chemical pre-treatment. It is an emerging pre-treatment route for mildly to moderately refractory concentrates.',
                'content'     => <<<'MD'
## What Is Mechanochemical Activation?

Mechanochemical activation (MCA) refers to the chemical and structural changes induced in solid materials by high-energy mechanical treatment — beyond simple size reduction. In the context of refractory gold, MCA uses planetary mills, attritor mills, or stirred media mills operated at very high energy densities to:

1. Introduce crystal lattice defects (vacancies, dislocations, stacking faults) into sulfide minerals
2. Amorphise sulfide mineral surfaces and, at sufficient energy, the bulk crystal structure
3. Oxidise sulfide surfaces in situ when milling is conducted in air or oxygen atmosphere
4. Break M–S bonds (Fe–S, As–S) at the surface, creating reactive defect sites that are more susceptible to subsequent cyanide attack

## Structural Changes

**X-ray diffraction** of mechanochemically activated pyrite shows progressive peak broadening (Scherrer equation crystallite size decreases from 50–100 nm in unactivated pyrite to 5–15 nm after 30–60 min planetary milling), indicating severe lattice distortion. At sufficiently high milling energy, the pyrite diffraction peaks disappear entirely — indicating complete amorphisation to a disordered FeS₂ glass.

**Mössbauer spectroscopy** of activated pyrite reveals the appearance of Fe³⁺ components that are absent in pristine pyrite — demonstrating that partial oxidation of Fe²⁺ occurs even in the absence of added oxidant, driven by the broken Fe–S bonds created during milling.

**BET surface area** increases dramatically with MCA: from ~1 m²/g (standard ball mill P₈₀ = 75 µm) to 10–50 m²/g (planetary mill, 60 min), increasing gold surface exposure proportionally.

## Effect on Cyanide Leachability

Lab studies on activated arsenopyrite-pyrite concentrates consistently show:
- Direct cyanidation gold recovery on unactivated concentrate: 30–50%
- Recovery after MCA (planetary mill, 60 min): 65–80%
- Recovery after POX: 90–96%

MCA thus bridges the performance gap between simple grinding and full chemical pre-treatment, at a fraction of the capital and operating cost.

## Mechanochemical Oxidation

If milling is conducted in oxygen or air atmosphere, mechanochemical reactions directly oxidise sulfide surfaces:
**FeS₂ + O₂ (mechanically activated) → FeSO₄ + Fe₂O₃ + amorphous FeS₂**

This "dry activation" approach oxidises the outer 5–20 nm of sulfide grains, creating an oxidised shell through which cyanide can more easily diffuse to reach gold underneath. Industrially, oxygen-atmosphere milling is achieved by purging the mill chamber with O₂ or air during operation.

## Wet vs. Dry Activation

**Wet MCA (slurry milling in air or O₂):** Slower oxidation but more uniform activation; allows simultaneous size reduction to P₈₀ = 5–15 µm. Currently the more industrially relevant approach.

**Dry MCA (powder milling):** Much faster activation per unit energy but requires subsequent reslurrying; energy input per tonne is higher; risk of dust explosion with fine sulfide powders.

## Industrial Prospects

MCA has not yet been deployed at commercial scale for gold specifically, but several analogues exist:
- Isamill at ultra-fine conditions (P₈₀ < 10 µm) achieves some mechanochemical activation as a side effect of its very high specific energy input (> 60 kWh/t)
- Laboratory studies at CSIRO, WASM, and the Ural Federal University (Russia) demonstrate strong recovery improvements
- Economic modelling suggests MCA is viable for concentrates with gold grades > 20 g/t (where the capital saving vs. POX/BIOX justifies accepting slightly lower recovery)
MD,
            ],

            // ── 16 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['arsenic', 'scorodite', 'environmental', 'acid mine drainage']),
                'title'       => 'Arsenic Management in Refractory Gold Processing: Chemistry, Stability, and Regulations',
                'summary'     => 'Arsenic is the dominant environmental liability in refractory gold processing. Understanding its speciation, the stability of arsenic-bearing residues (scorodite, ferric arsenate, arsenate-in-gypsum), and regulatory requirements is essential for plant design, tailings management, and long-term liability planning.',
                'content'     => <<<'MD'
## Sources of Arsenic in Refractory Gold Processing

Arsenic enters the process stream from arsenopyrite (FeAsS) and arsenian pyrite (FeS₂ with As substituting for S). During oxidative pre-treatment:

- **Pressure oxidation:** As³⁻ is oxidised to As⁵⁺ (arsenate, AsO₄³⁻) and remains in solution as H₃AsO₄/H₂AsO₄⁻ at process pH < 2. Typical POX discharge contains 1–20 g/L dissolved As.
- **Roasting:** As₂O₃ volatilises and must be captured in off-gas treatment; uncaptured As₂O₃ is highly toxic.
- **Biooxidation:** As³⁺ and As⁵⁺ accumulate in the bio-leach liquor at concentrations of 2–15 g/L.

## Arsenic Solid Phase Stability

The long-term environmental risk from arsenic depends entirely on the solid phase in which it is immobilised in the tailings.

**Scorodite (FeAsO₄·2H₂O):** Thermodynamically the most stable arsenic-bearing solid under ambient conditions. Forms during high-temperature POX (> 220 °C) or by deliberate precipitation from POX liquor at pH 1–2 and > 70 °C. Solubility < 1 mg/L As under TCLP test conditions. Recommended as the target immobilisation form by most environmental regulators.

**Amorphous ferric arsenate:** Precipitates from BIOX and low-temperature POX liquors on neutralisation. Solubility 5–50 mg/L — above drinking water standards (WHO 10 µg/L). Requires co-disposal with lime and monitoring for long-term leaching.

**Calcium arsenate (Ca₃(AsO₄)₂) and arsenate-in-gypsum:** Form when lime (CaCO₃/CaO) is used for neutralisation. Less stable than scorodite; solubility sensitive to pH and redox changes in the tailings environment.

**Arsenic adsorbed on ferrihydrite:** Common in mine waters; stable at pH 6–8, releases on acidification or reducing conditions (acid mine drainage). Not suitable as a terminal storage form.

## Precipitation and Immobilisation

**Scorodite precipitation route:**
1. POX discharge (pH 0–1, dissolved As 5–20 g/L) is fed to a CSTR at 70–95 °C
2. FeSO₄ or Fe₂(SO₄)₃ added to adjust Fe:As molar ratio to ~1:1
3. pH adjusted to 1.0–1.5 with lime or limestone
4. Crystalline scorodite nucleates and grows over 8–24 h residence time
5. Filter cake: > 30 wt% As, < 1 mg/L leachate As (TCLP) — suitable for tailings deposition

**Ferrihydrite co-precipitation:**
At pH 5–7, Fe³⁺ precipitates as ferrihydrite and strongly adsorbs arsenate. Easier to achieve but produces a less stable long-term residue. Used as a polishing step for mine water treatment.

## Regulatory Framework

| Jurisdiction | Drinking water limit (As) | Tailings/effluent discharge limit | Leachate limit (TCLP equivalent) |
|---|---|---|---|
| WHO | 10 µg/L | Country-specific | — |
| USA (EPA) | 10 µg/L | 10 µg/L (Clean Water Act) | 5 mg/L (RCRA TCLP) |
| Australia | 10 µg/L | 100–500 µg/L (state-specific) | 100 mg/L (ANZECC) |
| EU | 10 µg/L | 100 µg/L (IED) | 0.5 mg/L (WFD) |
| Russia | 50 µg/L | 50 µg/L | — |

The EU's Industrial Emissions Directive (IED) and Water Framework Directive (WFD) impose the most stringent arsenic controls globally, making EU-located or EU-financed refractory gold projects (which are rare) subject to the highest compliance cost.

## Long-Term Tailings Monitoring

Even stable scorodite slowly recrystallises and can release arsenic under:
- Reducing conditions (flooding, high organic matter) — As⁵⁺ → As³⁺ (more soluble and mobile)
- Extreme pH (< 2 or > 10) — scorodite dissolves
- Co-dissolution with sulfide-bearing tailings generating acid mine drainage (AMD)

Modern tailings facilities for refractory gold operations use engineered covers (reducing oxygen ingress), liner systems, and groundwater monitoring networks. Life-of-mine and post-closure arsenic management plans are now mandatory under IFC Performance Standards and most national mining regulations.
MD,
            ],

            // ── 17 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['geometallurgy', 'ore variability', 'block model', 'mine planning']),
                'title'       => 'Geometallurgy of Refractory Gold: Mapping Ore Variability and Linking Geology to Recovery',
                'summary'     => 'Geometallurgy integrates geological, mineralogical, and metallurgical data into spatial models that predict how gold recovery will vary across an ore body. For refractory gold deposits, where refractoriness intensity and type (sulfidic vs. carbonaceous) vary significantly in three dimensions, geometallurgical modelling is essential for mine planning, blending, and plant design.',
                'content'     => <<<'MD'
## Definition and Motivation

**Geometallurgy** is the discipline that links ore geology and mineralogy to metallurgical performance at the spatial resolution needed for mine-by-mine and year-by-year production planning. Its core output is a geometallurgical block model — a 3D grid where each block contains predicted gold grade, mineralogy, and expected gold recovery (or alternatively, the required pre-treatment intensity).

For a free-milling ore, a simple grade model may suffice. For a refractory gold ore, the recovery varies enormously depending on:
- Pyrite/arsenopyrite abundance (controls sulfide-locked gold fraction)
- Carbonaceous matter content (controls preg-robbing potential)
- Gold grain size distribution (controls liberation at a given grind)
- Carbonate gangue content (controls acid consumption in POX)
- Reactive sulfide content (controls heat balance in POX/roasting)

A mine without a geometallurgical model sends ore of unknown metallurgical character to the plant — leading to inconsistent recoveries, equipment overload/underload, and missed production targets.

## Key Geometallurgical Variables for Refractory Gold

**Sulfide sulfur (S²⁻):** Total sulfide sulfur by Leco combustion analysis. Proxy for sulfide abundance, heat generation in autoclaves, oxygen demand, and acid production. Mapped from drill core assay databases.

**Total organic carbon (TOC):** Leco combustion on acid-washed sample (to remove carbonate carbon). Proxy for preg-robbing potential. Spatial distribution of TOC often strongly correlated with original sedimentary facies (organic shales vs. clean limestone).

**Diagnostic leach gold deportment:** At 30–100 sample points through the ore body, run full diagnostic leach protocol. Map spatial distribution of free vs. sulfide-locked vs. carbon-adsorbed gold fractions. Interpolate between sample points using geostatistical methods (kriging or sequential Gaussian simulation).

**Arsenic:** Proxy for arsenopyrite abundance; also controls arsenic management requirements and plant discharge specifications.

**Acid-consuming gangue (ACG):** Carbonate content by carbon dioxide analysis or acid titration. Determines lime consumption for POX neutralisation — often the controlling economic variable for POX plant operating cost.

## Geometallurgical Testing Program

A typical feasibility-study geometallurgical program for a 100 Mt refractory gold deposit involves:

1. **Geometallurgical variability samples:** 50–100 composite samples selected to span the range of geological variability (ore types, depths, lithologies). Each sample undergoes: head assay, mineralogical characterisation (QEMSCAN/MLA), diagnostic leach, preg-robbing index.

2. **Pre-treatment response testing:** 5–10 key ore type composites undergo mini-autoclave POX, BIOX flask testing, or tube furnace roasting to quantify recovery improvement vs. oxidation extent. Defines the minimum pre-treatment intensity for each ore type.

3. **Locked-cycle tests (LCT):** Continuous lab simulations of the full circuit (flotation + pre-treatment + cyanidation) for 2–3 ore type composites, defining steady-state recovery, reagent consumption, and plant mass balance.

4. **Spatial interpolation:** Geometallurgical parameters are interpolated into the block model using domained kriging or co-kriging with correlated variables (e.g., As grade as a co-variable for gold deportment interpolation).

## Application in Mine Planning

The geometallurgical block model enables:
- **Ore blending:** Identifying optimal blends of high-sulfide and low-sulfide blocks to maintain consistent POX feed composition and heat balance.
- **Pre-treatment sequencing:** Scheduling blocks requiring different pre-treatment intensities (some may bypass POX entirely; some may require double pre-treatment).
- **Life-of-mine recovery prediction:** Year-by-year recovery forecasts that account for ore type changes as mining progresses deeper — critical for financial modelling and covenant compliance.
- **Plant capacity design:** Ensuring autoclave/roaster capacity matches the peak pre-treatment demand, not just average demand.

## Case Study: Goldstrike (Barrick, Nevada)

Goldstrike developed one of the first comprehensive refractory gold geometallurgical models in the mid-1990s, integrating:
- TOC and sulfide S assays on all drill core
- Preg-robbing index on 60 variability composites
- Flotation and POX/roaster response on 15 ore type composites

The model revealed that ~30% of ore by mass contained sufficient TOC to cause significant preg-robbing even after POX — leading to the decision to install the thiosulfate circuit rather than rely solely on activated carbon.
MD,
            ],

            // ── 18 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['gold tellurides', 'calaverite', 'telluride mineralogy']),
                'title'       => 'Gold Tellurides: A Distinct Class of Refractory Gold with Unique Processing Challenges',
                'summary'     => 'Gold telluride minerals (calaverite, krennerite, sylvanite, petzite) are a distinct and partially refractory gold ore type that resists standard cyanidation due to slow cyanide dissolution kinetics. They are the primary gold host at several world-class deposits including Kalgoorlie (Australia), Cripple Creek (USA), and Emperor (Fiji).',
                'content'     => <<<'MD'
## Telluride Mineralogy

Gold tellurides are intermetallic compounds of gold (and silver) with tellurium. The principal species are:

| Mineral | Formula | Au content | Crystal system |
|---|---|---|---|
| Calaverite | AuTe₂ | 44 wt% Au | Monoclinic |
| Krennerite | (Au,Ag)Te₂ | ~39–44 wt% Au | Orthorhombic |
| Sylvanite | (Au,Ag)Te₄ | ~25 wt% Au | Monoclinic |
| Petzite | Ag₃AuTe₂ | ~19 wt% Au | Isometric |
| Hessite | Ag₂Te | — (Ag only) | Monoclinic |
| Montbrayite | (Au,Sb)₂Te₃ | ~33 wt% Au | Triclinic |

**Calaverite** is the most economically important, being the dominant gold mineral at Kalgoorlie's Golden Mile (Australia) and at Cripple Creek (Colorado). The Golden Mile alone has produced > 60 Moz of gold, largely from calaverite.

## Why Tellurides Are "Refractory"

Calaverite and other gold tellurides dissolve in cyanide solution, but the kinetics are much slower than for native gold under standard leach conditions:

**Native Au:** Dissolution rate ~10–50 µm/h (at 200–500 ppm CN⁻, pH 10.5)
**Calaverite:** Dissolution rate ~0.5–2 µm/h under identical conditions

The slow kinetics arise because:
1. The outer surface of a dissolving calaverite grain becomes coated with a passivating layer of amorphous tellurium (Te⁰)
2. Te⁰ is not dissolved by cyanide and must be removed by oxidation for gold dissolution to continue
3. This "tellurium passivation" effect limits the effective gold dissolution rate to the rate of Te⁰ removal

The degree of refractoriness depends on grain size: calaverite grains < 10 µm dissolve adequately in standard 24-h cyanide leach; grains > 50 µm require pre-treatment.

## Processing of Gold Tellurides

**Roasting (primary commercial approach):**
Roasting calaverite-bearing concentrates at 450–550 °C oxidises both Te and S:
AuTe₂ + O₂ → Au⁰ + TeO₂
TeO₂ volatilises above 450 °C; gold remains as fine metallic particles in the calcine.
Gold recovery from roasted calaverite calcine by cyanidation: > 95%.
Tellurium dioxide (TeO₂) can be recovered from the off-gas for sale as a by-product (tellurium is a critical mineral used in solar cells, thermoelectrics, and electronics).

**Oxidative pre-treatment (H₂O₂ or hypochlorite):**
Strong oxidants dissolve the Te⁰ passivation layer:
3 Te⁰ + 3 H₂O₂ → 3 TeO + 3 H₂O → tellurate complexes
After oxidative conditioning, calaverite dissolves readily in cyanide. Used at some operations as a supplementary step rather than full roasting.

**Pressure oxidation:** Also effective; high-temperature POX destroys calaverite structure and releases gold with TeO₄²⁻ in solution.

**Alkaline sulfide leaching:** At pH > 12 in the presence of Na₂S, tellurides dissolve:
AuTe₂ + Na₂S → Na₂Te + Au(S complexes)
Used historically and in some experimental programs; selectivity over sulfide gangue is imperfect.

## Tellurium By-Product Recovery

Tellurium is a critical material: primary supply is dominated by by-product recovery from copper anode slimes. Gold telluride ores represent an important secondary source. At Kalgoorlie's Fimiston plant (KCGM), tellurium is recovered from roaster off-gas as TeO₂, purified, and sold.

Tellurium demand is rising with the growth of CdTe solar panels (First Solar's technology uses ~94 tonnes Te per GW of panel capacity). This creates an economic incentive to recover tellurium from gold telluride ores that was absent a decade ago, potentially shifting the economics of roasting vs. POX decisions for calaverite ores.
MD,
            ],

            // ── 19 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['geometallurgical modelling', 'process selection', 'techno-economics', 'feasibility']),
                'title'       => 'Economic Threshold of Refractory Gold Processing: When Is Pre-Treatment Viable?',
                'summary'     => 'Pre-treatment for refractory gold adds significant capital and operating cost. The decision to pre-treat, and which technology to select, is fundamentally an economic optimisation that depends on gold price, ore grade, recovery improvement, and project scale. This entry frames the decision framework used by mining engineers.',
                'content'     => <<<'MD'
## The Core Economic Equation

The value of pre-treatment is:
**ΔNPVₚᵣₑₜᵣₑₐₜ = (ΔAu_recovery × Au_price × ore_tonnes) − (CAPEX + OPEX_pretx)**

Where ΔAu_recovery is the fractional improvement in gold recovery enabled by pre-treatment over direct cyanidation.

The decision threshold: if pre-treatment improves NPV (net present value) by more than the risk premium of increased capital exposure, pre-treatment is economically justified.

## Capital Cost Benchmarks (2024 USD)

| Technology | Typical CAPEX range | Basis |
|---|---|---|
| Ultra-fine grinding (UFG) | USD 15–50 M | 50–200 t/h concentrate feed |
| BIOX (concentrate scale) | USD 40–120 M | 100–500 t/d concentrate |
| Roasting (fluid bed) | USD 80–250 M | 500–2000 t/d concentrate |
| Pressure oxidation (POX) | USD 150–600 M | 500–3000 t/d concentrate |
| POX (whole ore, large) | USD 500 M–2 B | 5000–20,000 t/d whole ore |

Capital costs scale with the sulfide sulfur throughput, not gold grade — a useful proxy for initial scoping.

## Operating Cost Benchmarks

| Technology | Typical OPEX | Dominant cost items |
|---|---|---|
| UFG | USD 5–15/t concentrate | Power (grinding) |
| BIOX | USD 30–60/t concentrate | Nutrients, acid, oxygen, cooling |
| Roasting | USD 40–90/t concentrate | Fuel/power, acid plant O&M, As disposal |
| POX (concentrate) | USD 80–150/t concentrate | Oxygen plant, autoclave maintenance, reagents |
| POX (whole ore) | USD 8–25/t ore | Oxygen, autoclave maintenance, lime neutralisation |

## Break-Even Analysis: Grade vs. Recovery

A simplified break-even analysis for adding POX to a project:

**Given:**
- Gold price: USD 2000/oz
- Direct cyanidation recovery: 50%
- POX + cyanidation recovery: 93%
- POX CAPEX: USD 200 million; OPEX premium: USD 20/t ore; mine life: 15 years
- Ore throughput: 5 Mtpa

**Annual incremental revenue from POX:**
= 5 × 10⁶ t × Au_grade × (0.93 − 0.50) × 2000 USD/oz / (31.1 g/oz)
= 5 × 10⁶ × g/t × 0.43 × 64.3 USD/g
= g/t × 138 MUSD/year

**Annual incremental OPEX cost:**
= 5 × 10⁶ × 20 = USD 100 million/year

**Break-even grade (where incremental revenue = incremental OPEX):**
g/t × 138 = 100 → g/t = 0.72 g/t Au

POX is economically viable at grades > ~0.7 g/t Au (at USD 2000/oz gold price) for this scenario — confirming that even many low-grade deposits can justify POX at today's gold prices.

## Technology Selection Decision Tree

**Step 1:** Is ore free-milling (RI < 0.10)? → No pre-treatment needed. If RI > 0.10, continue.

**Step 2:** Is refractoriness primarily from carbonaceous matter (PRI > 0.20) without significant sulfide lock? → Carbon passivation or CIL with high carbon loading. If significant sulfide lock, continue.

**Step 3:** Is the ore high-grade (> 5 g/t) or does the project produce a flotation concentrate? → POX or BIOX on concentrate. If low-grade whole ore, continue.

**Step 4:** Is sulfide content low–moderate (< 4% S)? And Is carbon preg-robbing low (PRI < 0.15)? → BIOX or roasting on whole ore. If high sulfide content or high preg-robbing, → POX.

**Step 5:** Is the project in a jurisdiction with strict SO₂ or As₂O₃ emissions limits? → POX preferred over roasting.

## Risk Factors

- **Gold price sensitivity:** A USD 500/oz fall in gold price reduces the break-even grade threshold proportionally. Projects marginal at USD 2000/oz may not be viable at USD 1500/oz.
- **Capex overrun risk:** POX plants are complex; cost overruns of 20–40% are common (Barrick's Pascua-Lama POX overran significantly; Kinross' Tasiast POX was revised multiple times).
- **Oxygen supply reliability:** POX is critically dependent on continuous oxygen supply; a 48-hour ASP outage stops the entire pre-treatment circuit.
- **Arsenic disposal liability:** Future tightening of arsenic disposal regulations could impose unforeseen costs on existing POX operations.
MD,
            ],

            // ── 20 ──────────────────────────────────────────────────────────────────
            [
                'category_id' => $categoryId,
                'status'      => 'published',
                'tags'        => array_merge($tags, ['future technology', 'electrochemical oxidation', 'nitric acid leach', 'chlorine leach', 'innovations']),
                'title'       => 'Emerging and Future Technologies for Refractory Gold Processing (2020–2025)',
                'summary'     => 'Beyond the established pre-treatment triad of POX, roasting, and BIOX, a wave of emerging technologies is approaching commercial readiness — including electrochemical oxidation, concentrated solar energy roasting, high-pressure glycine leaching, controlled-potential cyanidation, and high-temperature thermophilic bioheap leaching.',
                'content'     => <<<'MD'
## 1. Electrochemical Oxidation (ECO)

Electrochemical oxidation uses electrolytic cells to generate strong oxidants (Cl₂, ClO⁻, O₃, or •OH radicals) in situ from sulfate/chloride electrolyte — oxidising sulfide mineral surfaces and liberating gold without the need for an autoclave, furnace, or bacterial culture.

**Mechanism:** At the anode, Cl⁻ → Cl₂ (in chloride media) or H₂O → O₃/•OH (in sulfate media). These oxidants attack pyrite and arsenopyrite surfaces at ambient temperature and pressure.

**Current status:** Platinum Group Metals Ltd (PGML, Australia) and Goldfields have piloted electrochemical pre-treatment for refractory concentrates at 10–50 kg scale. Lab results show 80–92% gold recovery from moderately refractory arsenopyrite concentrates after 4–8 h treatment — comparable to BIOX. Capital cost projected at 30–50% of equivalent BIOX plant. Key challenge: energy consumption (150–300 kWh/t concentrate) and electrode wear under oxidising conditions.

## 2. Hypochlorite (HOCl/NaOCl) Leaching

In the **Haber Gold Process** (licensed from Haber Inc.) and similar systems, alkaline hypochlorite leaches gold directly from oxidised or mildly refractory ores as gold chloride [AuCl₄]⁻. Hypochlorite is generated on-site by electrolysis of brine.

**Advantages:** No cyanide; no high-pressure vessels; gold recovery from some preg-robbing ores competitive with CIL.
**Limitations:** Hypochlorite attacks carbonaceous matter and organic matter, generating chlorinated by-products that may require treatment. Not effective for highly sulfidic ores. Instability of hypochlorite at pH > 11 limits applicability.

## 3. Nitric Acid Leaching

Dilute nitric acid (2–8%) selectively dissolves arsenopyrite and pyrrhotite more rapidly than pyrite, partially liberating gold from mixed-sulfide concentrates without the energy demands of POX. A subsequent mild cyanide leach recovers liberated gold.

Gold recovery: 70–85% for arsenopyrite-dominant concentrates in lab tests. Key concern: NOₓ gas generation during nitric acid attack on sulfides; scrubbing required. Potentially attractive for concentrate treatment in remote locations without oxygen supply infrastructure.

## 4. Concentrated Solar Energy for Roasting

**Solar roasting** uses parabolic-trough or heliostat solar concentrators to supply thermal energy for sulfide oxidation, replacing fossil fuel combustion in the calcination furnace. Research groups at the University of Mons (Belgium) and CSIRO (Australia) have demonstrated solar roasting of pyrite–arsenopyrite concentrates at pilot scale (10–50 kg batches) achieving equivalent calcine quality to conventional roasters.

At gold mine locations in high-irradiance regions (Nevada, Western Australia, Chile, West Africa), solar input can offset 60–80% of furnace fuel cost. The intermittency of solar supply requires thermal storage (molten salts or graphite blocks) or hybrid fossil/solar operation.

## 5. Atmospheric Alkaline Glycine–Peroxide Leaching

Building on the glycine leaching work described separately, the **glycine–H₂O₂** system at atmospheric pressure has shown promising results for moderately refractory ores:

- 70–88% gold recovery from arsenopyrite concentrates in 24–48 h (Oraby et al., 2019, *Minerals*)
- No cyanide; no pressure vessels
- Glycine recovered by nanofiltration for recycle

The key research frontier (2023–2025): reducing H₂O₂ consumption by using electrogenerated peroxide (from oxygen reduction on graphite electrodes) rather than purchased H₂O₂, reducing operating cost from ~USD 15/t to ~USD 5/t.

## 6. High-Temperature Thermophilic Heap Biooxidation

Standard BIOX uses mesophilic bacteria (35–40 °C) in stirred tanks. For lower-grade ores and larger tonnages, **heap biooxidation** applies acidophilic bacteria to crushed ore stacked in 6–10 m heaps — similar to heap leaching for oxide gold, but using microbial sulfide oxidation as the liberation mechanism.

**Thermophilic heap biooxidation** (60–75 °C using archaea such as *Sulfolobus metallicus*) achieves faster oxidation rates than mesophilic heaps, completing sulfide oxidation in 60–120 days vs. 150–300 days for mesophilic systems. Mintek/BacTech's HIOX process and CSIRO's thermophilic heap leach program are the most advanced developments (2022–2025 pilot scale).

Gold recovery from thermophilic bioheaps: 75–88% (vs. 60–75% for mesophilic heaps). Economically viable for grades > 0.8 g/t Au, significantly lower than the > 1.5 g/t threshold for BIOX tank processes.

## 7. Controlled-Potential Cyanidation

In standard cyanide leaching, the oxidation potential (Eh) of the pulp is not actively controlled, leading to over-oxidation of sulfide surfaces that passivates gold and under-dissolution of gold in pyrrhotite-bearing ores. **Controlled-potential cyanidation (CPC)** maintains pulp Eh at 300–400 mV (SHE) using hydrogen peroxide or oxygen addition modulated by a PLC, ensuring optimal gold dissolution kinetics while minimising reagent consumption.

Lab and pilot results: 5–15% recovery improvement over conventional cyanidation on mildly refractory pyrrhotite ores; 5–10% cyanide consumption reduction. Commercial installation at one Australian gold mine (name withheld under NDA) since 2023.

## 8. Machine Learning for Process Optimisation

**Digital twin** models of POX autoclaves and BIOX tank arrays, trained on historical sensor data and process chemistry models, are being deployed to optimise:
- Oxygen partial pressure profiles along the autoclave length
- Temperature zoning to avoid over-roasting
- Bacteria population dynamics and nutrient dosing in BIOX
- Blending optimisation to maintain consistent pre-treatment feed

Barrick Gold's Digital Centre of Excellence (2020–2024) reported 2–4% improvement in overall gold recovery at Goldstrike from ML-driven optimisation of the thiosulfate leach circuit — roughly USD 20 million/year additional revenue.
MD,
            ],

        ];
    }
}
