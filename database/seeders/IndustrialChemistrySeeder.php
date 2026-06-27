<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class IndustrialChemistrySeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['name' => 'Industrial Chemistry'],
            ['slug' => 'industrial-chemistry']
        );

        $entries = [
            [
                'title'   => 'Reactor Design Fundamentals: CSTR, PFR, and Batch Reactors',
                'summary' => 'A comparative overview of the three principal reactor types used in industrial chemistry, covering design equations, residence time distribution, and selection criteria.',
                'tags'    => ['reactor design', 'CSTR', 'PFR', 'batch reactor', 'chemical engineering', 'residence time'],
                'content' => <<<MD
# Reactor Design Fundamentals: CSTR, PFR, and Batch Reactors

Industrial chemical reactors transform raw materials into products through controlled reaction environments. Selecting the correct reactor type affects yield, selectivity, safety, and capital cost.

## The Three Canonical Reactor Types

### Continuous Stirred Tank Reactor (CSTR)
- Perfectly mixed assumption: composition and temperature are uniform and equal to the outlet stream.
- **Design equation**: V = F_A0 · X / (−r_A)_exit, where X is conversion, F_A0 is molar feed rate, and (−r_A)_exit is the exit reaction rate.
- Best for: slow reactions, highly exothermic reactions (easy temperature control), viscous liquid-phase systems, biological fermentation.
- Weakness: For a given volume, achieves lower conversion than a PFR because it operates at the lowest (exit) concentration.

### Plug Flow Reactor (PFR)
- No axial mixing assumption: fluid elements move as a "plug" with no back-mixing.
- **Design equation**: V = F_A0 ∫(dX / (−r_A)) from 0 to X.
- Best for: gas-phase reactions, fast reactions requiring high conversion, systems where selectivity favours high reactant concentration.
- Weakness: Poor temperature control for highly exothermic reactions (hot spots); difficult to operate for solid-catalysed reactions without pressure drop.

### Batch Reactor
- No continuous feed/outlet during reaction; charged, reacted, then discharged.
- **Design equation**: t = N_A0 ∫(dX / (−r_A · V)) from 0 to X.
- Best for: specialty chemicals, pharmaceuticals, small-volume high-value products, reactions requiring long residence times.
- Weakness: Downtime for charging/discharging/cleaning; difficult scale-up.

## Residence Time Distribution (RTD)

RTD characterises how long different fluid elements spend in a reactor:
- **E(t) curve**: Exit age distribution from a pulse tracer input.
- **F(t) curve**: Cumulative distribution from a step tracer input.
- Mean residence time: τ = V / Q (volumetric flow rate).
- CSTR: E(t) = (1/τ) exp(−t/τ) — exponential decay.
- PFR: E(t) = δ(t − τ) — Dirac delta at mean residence time.

## Multiple Reactor Configurations

| Configuration | Use Case |
|--------------|----------|
| CSTRs in series | Approaches PFR performance; better selectivity than single CSTR |
| PFR with cold shots | Manages exothermic reactions by injecting cold feed between stages |
| Recycle PFR | Adjusts RTD between PFR and CSTR; useful for autocatalytic reactions |

## Scale-up Challenges

- Maintaining mixing time < reaction time at large scale.
- Heat transfer area:volume ratio decreases with scale — large CSTRs need internal coils or external heat exchangers.
- Pressure drop increases with PFR length — catalyst bed design critical.
MD,
            ],

            [
                'title'   => 'Catalysis: Homogeneous, Heterogeneous, and Enzymatic Systems',
                'summary' => 'How catalysts accelerate reactions without being consumed, contrasting homogeneous and heterogeneous catalysis, and the emerging role of biocatalysis in industrial processes.',
                'tags'    => ['catalysis', 'heterogeneous catalyst', 'homogeneous catalyst', 'biocatalysis', 'reaction rate', 'industrial chemistry'],
                'content' => <<<MD
# Catalysis: Homogeneous, Heterogeneous, and Enzymatic Systems

A catalyst increases reaction rate by providing an alternative lower-activation-energy pathway. It is not consumed in the overall reaction, though it may participate in intermediate steps and can degrade over time (catalyst deactivation).

## Homogeneous Catalysis

Catalyst and reactants are in the same phase (typically liquid).

**Advantages**: Well-defined active sites; high selectivity; amenable to mechanistic study.
**Disadvantages**: Separation of catalyst from product is costly; temperature limits often below heterogeneous systems.

**Industrial examples**:
- **Wacker process**: Pd(II)/Cu(II) chloride in aqueous solution catalyses ethylene oxidation to acetaldehyde.
- **Ziegler–Natta in solution**: Titanocene/aluminoxane catalysts for stereospecific polymerisation.
- **Rhodium-phosphine complexes**: Monsanto and Cativa processes for acetic acid production (methanol carbonylation).

## Heterogeneous Catalysis

Catalyst is in a different phase than reactants, usually a solid catalyst with gas or liquid reactants.

**Advantages**: Easy separation; high thermal stability; continuous operation; amenable to fixed-bed or fluidised-bed reactors.
**Disadvantages**: Mass transfer limitations; active site heterogeneity makes mechanistic understanding harder.

**Key steps at the solid surface (Langmuir–Hinshelwood mechanism)**:
1. External mass transfer of reactant to catalyst surface
2. Pore diffusion (internal mass transfer)
3. Adsorption on active site
4. Surface reaction
5. Desorption of product
6. Pore and external diffusion of product away

The slowest step controls the overall rate — the **rate-determining step**.

**Industrial examples**:
- **Haber–Bosch process**: Fe/K₂O/Al₂O₃ catalyst; N₂ + 3H₂ → 2NH₃ at 400–500 °C, 150–300 bar.
- **Catalytic cracking (FCC)**: Zeolite Y catalyst; breaks heavy oil fractions into gasoline-range molecules.
- **Three-way automotive catalyst**: Pt/Pd/Rh on alumina washcoat; simultaneous oxidation of CO and HC, reduction of NOₓ.

## Enzymatic (Biocatalysis)

Enzymes are protein catalysts operating under mild aqueous conditions (20–60 °C, near-neutral pH).

**Advantages**: Extraordinary selectivity (chemo-, regio-, stereo-specific); biodegradable; green chemistry credentials.
**Disadvantages**: Limited to conditions compatible with protein stability; often require co-factors (NAD⁺, ATP).

**Industrial applications**:
- **Lipases**: Biodiesel production (fatty acid transesterification), ester synthesis for flavours and fragrances.
- **Proteases**: Detergent formulation (Savinase, Alcalase); food processing.
- **Glucose isomerase**: Conversion of glucose to fructose for high-fructose corn syrup.
- **Directed evolution**: Protein engineering to expand substrate range, improve thermostability (Frances Arnold Nobel Prize 2018).

## Catalyst Deactivation

| Mechanism | Cause | Remedy |
|-----------|-------|--------|
| Poisoning | Strong adsorption of impurity (e.g., S, Pb on Pt) | Feed purification |
| Sintering | Thermal agglomeration of metal particles | Operate below Tammann temperature |
| Coking | Carbon deposition on acid sites | Regeneration by controlled combustion |
| Leaching | Active metal dissolves (homogeneous) | Ligand design, pH control |
MD,
            ],

            [
                'title'   => 'Distillation Column Design: Tray vs Packed Columns and Key Performance Metrics',
                'summary' => 'Principles of continuous distillation, comparison of tray and packed column internals, and the key design parameters — HETP, flooding, and efficiency — that govern separation performance.',
                'tags'    => ['distillation', 'separation', 'tray column', 'packed column', 'HETP', 'flooding', 'industrial chemistry'],
                'content' => <<<MD
# Distillation Column Design: Tray vs Packed Columns and Key Performance Metrics

Distillation is the most widely used separation technique in the chemical and petroleum industries, exploiting differences in component volatilities to achieve separation. Column design determines the capital cost, operating cost, and achievable purity.

## Fundamentals

**Relative volatility** (α) between components A and B:
α_AB = (y_A / x_A) / (y_B / x_B) = P_A^sat / P_B^sat (Raoult's Law for ideal mixtures)

Higher α → easier separation → fewer theoretical stages required.

**McCabe–Thiele method**: Graphical construction on a y-x diagram to determine the number of theoretical stages at a given reflux ratio.

**Minimum reflux ratio** (R_min): At this ratio, infinite stages are required (pinch point). Operating reflux is typically 1.1–1.5 × R_min.

## Tray Columns

Discrete stages where vapour and liquid are brought into contact on perforated plates (sieve trays), valve trays, or bubble-cap trays.

**Advantages**: Handles high liquid loads; suitable for fouling/corrosive systems; easier cleaning and inspection.

**Key parameters**:
- **Tray efficiency** (Murphree): E_MV = (y_n − y_{n+1}) / (y_n* − y_{n+1}) — actual vs equilibrium enrichment per tray.
- **Downcomer**: Allows liquid to flow between trays without entrainment.
- **Weir height**: Controls liquid level on tray; affects vapour–liquid contact time.

**Operational limits**:
- **Flooding**: Excessive vapour velocity causes liquid carry-over (entrainment flooding) or liquid backup into the vapour space (downcomer flooding). Design at 70–80% of flood velocity.
- **Weeping**: Too-low vapour velocity causes liquid to fall through tray perforations — loss of vapour–liquid contact.

## Packed Columns

Continuous vapour–liquid contact through structured or random packing.

| Packing Type | HETP Range | Pressure Drop | Best Use |
|-------------|-----------|---------------|----------|
| Random (Raschig rings, Pall rings) | 300–600 mm | Moderate | General duty, retrofits |
| Structured (Sulzer MellapakPlus) | 100–300 mm | Low | Vacuum distillation, high purity |
| Grid packing | 500–700 mm | Very low | Atmospheric crude distillation |

**HETP** (Height Equivalent to a Theoretical Plate): Lower HETP = more efficient packing.

**Advantages over trays**: Lower pressure drop (critical for vacuum columns); better for temperature-sensitive products; higher capacity per unit cross-section.

**Disadvantages**: Susceptible to maldistribution (liquid channelling through packing); requires good liquid distributors; harder to clean.

## Liquid Distributors

Poor liquid distribution is the primary cause of below-design packed column performance. Distributors must achieve:
- **Drip point density**: ≥100 points/m² for structured packing.
- **Flow uniformity**: <5% variation across cross-section.

Types: orifice pan, trough distributor, spray nozzle. Redistributors required every 5–8 m of packing depth.

## Energy Integration

Distillation is energy-intensive: reboiler duty typically 2–4 GJ per tonne of product. Energy recovery strategies:
- **Feed preheating** using overhead vapour (feed-effluent heat exchange).
- **Heat-integrated distillation columns** (HIDiC): Internal heat pump between rectifying and stripping sections.
- **Dividing-wall columns**: Single column body achieves three-product separation with ~30% energy saving.
MD,
            ],

            [
                'title'   => 'Industrial Gas Separation: Cryogenic Distillation, PSA, and Membrane Technology',
                'summary' => 'How large-scale gas separation is achieved using three complementary technologies — cryogenic distillation, pressure swing adsorption, and membrane permeation — and the trade-offs in purity, recovery, and scale.',
                'tags'    => ['gas separation', 'PSA', 'membrane', 'cryogenic', 'nitrogen', 'hydrogen', 'industrial chemistry'],
                'content' => <<<MD
# Industrial Gas Separation: Cryogenic Distillation, PSA, and Membrane Technology

Industrial gas separation produces pure nitrogen, oxygen, hydrogen, CO₂, and noble gases at scales from a few litres per hour (on-site generators) to hundreds of tonnes per day (world-scale air separation units).

## Cryogenic Distillation

The dominant technology for large-scale N₂/O₂ separation and LNG production.

**Principle**: Liquefy air (Linde–Hampson cycle or Claude cycle), then distil in two columns:
- **High-pressure column** (~5 bar): Produces nitrogen overhead and oxygen-enriched liquid at base.
- **Low-pressure column** (~1.3 bar): Final separation; produces >99.5% N₂ overhead and >99.5% O₂ at base.

**Energy requirement**: ~0.3–0.4 kWh/Nm³ O₂ (highly optimised integrated plants).

**When to use**: Outputs >50 t/day; product purity >99%; multiple products (N₂ + O₂ + Ar simultaneously).

## Pressure Swing Adsorption (PSA)

Cyclic adsorption process exploiting differential adsorbent affinity at different pressures.

**N₂ PSA** (O₂ production, 90–95% purity):
- Adsorbent: Zeolite 13X or LiX; preferentially adsorbs N₂ at high pressure.
- **Adsorption step**: Feed air at 3–8 bar — O₂ passes through, N₂ adsorbs.
- **Regeneration step**: Depressurise to atmospheric — N₂ desorbs and purges.
- Typical cycle time: 60–120 seconds; ≥2 beds for continuous output.

**H₂ PSA** (refinery, 99.9%+ purity):
- Adsorbents: Activated carbon, zeolite in sequence; remove CO₂, CO, CH₄, H₂O.
- Hydrogen does not adsorb significantly — passes through at near-feed purity.

**Advantages**: No moving parts (except valves); low capital cost at small-medium scale; rapid start-up.
**Disadvantages**: Purity limited vs cryogenic; significant power consumption for compression.

## Membrane Separation

Thin polymer or inorganic membranes through which gas components permeate at different rates driven by partial pressure differential.

**Permeability and selectivity**:
- Permeability P = D × S (diffusivity × solubility).
- Selectivity α = P_A / P_B.
- **Upper bound trade-off** (Robeson plot): Higher selectivity generally comes with lower permeability.

**Common membrane materials and applications**:
| Material | Gas Pair | Selectivity | Application |
|----------|----------|-------------|-------------|
| Polysulfone | O₂/N₂ | ~4 | N₂ generation for inerting |
| Cellulose acetate | CO₂/CH₄ | ~20 | Natural gas sweetening |
| Polybenzimidazole (PBI) | H₂/CO | ~40 | Refinery H₂ recovery |
| Zeolite membranes | H₂O/ethanol | >100 | Dehydration of solvents |

**Advantages**: No phase change; low energy; modular; low maintenance.
**Disadvantages**: Trade-off between purity and recovery; polymeric membranes plasticise under high CO₂; limited chemical resistance.

## Technology Selection Guide

| Criterion | Cryogenic | PSA | Membrane |
|-----------|-----------|-----|---------|
| Scale | >50 t/day | 0.1–20 t/day | <5 t/day |
| Purity | >99.5% | 90–99.9% | 95–99% |
| Recovery | High (>95%) | Moderate (80–90%) | Moderate (70–85%) |
| Capital cost | Very high | Medium | Low |
| Start-up time | Hours | Minutes | Seconds |
MD,
            ],

            [
                'title'   => 'Process Safety: HAZOP Methodology and Layer of Protection Analysis',
                'summary' => 'How HAZOP studies systematically identify process deviations and hazards, and how LOPA quantifies whether existing safeguards are sufficient to meet risk targets.',
                'tags'    => ['process safety', 'HAZOP', 'LOPA', 'risk assessment', 'industrial chemistry', 'safety instrumented system'],
                'content' => <<<MD
# Process Safety: HAZOP Methodology and Layer of Protection Analysis

Process hazard analysis techniques are mandatory for facilities handling hazardous materials above specified threshold quantities. HAZOP and LOPA are the most widely applied methods in the chemical process industry.

## HAZOP (Hazard and Operability Study)

A structured team-based review that identifies hazards by applying guide words to process parameters at each node (defined section of the P&ID).

**Standard guide words**:
| Guide Word | Meaning | Example Deviation |
|-----------|---------|------------------|
| No / None | Complete negation of intent | No flow |
| More | Quantitative increase | High pressure |
| Less | Quantitative decrease | Low temperature |
| As Well As | Qualitative increase / additional | Contamination |
| Part Of | Qualitative decrease | Low concentration |
| Reverse | Opposite of intent | Reverse flow |
| Other Than | Complete substitution | Wrong material |

**HAZOP session output**:
For each deviation: cause → consequence → existing safeguards → recommendations (action, responsible person, target date).

**Team composition**: Process engineer (lead), instrument engineer, operations, safety, project (for new designs), maintenance.

**When to conduct**:
- New plant: P&ID at approximately 30% design completion (concept) and at IFC (issued-for-construction) stage.
- Existing plant: Revalidate every 5 years, or after significant process modification (Management of Change).

## Layer of Protection Analysis (LOPA)

Semi-quantitative risk assessment that follows HAZOP to determine whether existing safeguards reduce risk to acceptable levels, or whether additional independent protection layers (IPLs) — particularly Safety Instrumented Functions (SIFs) — are required.

**Tolerable risk criteria** (typical industry values):
- Catastrophic event (fatality): 10⁻⁴ to 10⁻⁶ per year.
- Serious injury: 10⁻³ per year.

**LOPA calculation**:
Mitigated event frequency = Initiating event frequency × ∏ PFD_IPL

Where PFD (Probability of Failure on Demand) for each IPL is determined by the type:

| IPL Type | Typical PFD Range |
|----------|------------------|
| BPCS (basic process control system) | 10⁻¹ (not an IPL if it's also the initiator) |
| Pressure relief valve | 10⁻² |
| SIS (SIL 1) | 10⁻¹ to 10⁻² |
| SIS (SIL 2) | 10⁻² to 10⁻³ |
| Dike / bund | 10⁻² |
| Human response (trained, >10 min) | 10⁻¹ |

**Independence requirement**: IPLs must be independent of the initiating cause and of each other. The BPCS sensor that detects the initiating condition cannot also be credited as an IPL.

## SIL Determination

If LOPA determines the risk is unacceptable, a Safety Instrumented Function (SIF) is designed to provide the required risk reduction. IEC 61511 requires:

1. SIL determination (LOPA or risk graph).
2. SIS design to meet the required SIL (PFD target).
3. Proof-test interval to maintain SIL over plant life.
4. Functional safety assessment (FSA) by competent person.

**SIL 2 example**: A high-pressure shutdown on a reactor requires PFD ≤ 10⁻² per demand. With a 1-oo-2 sensor voting and annual proof testing of a 2-valve final element, the SIS can achieve PFD ≈ 5×10⁻³ — meeting SIL 2.

## Key Standards

- IEC 61511 (functional safety, process industry)
- IEC 61508 (functional safety, generic — governs equipment suppliers)
- API RP 14C (oil and gas surface facilities)
- OSHA PSM 29 CFR 1910.119 (US regulatory requirement for covered processes)
MD,
            ],

            [
                'title'   => 'Polymer Synthesis: Addition vs Condensation Polymerisation',
                'summary' => 'The fundamental mechanisms of chain-growth and step-growth polymerisation, key industrial polymers produced by each route, and how molecular weight distribution is controlled.',
                'tags'    => ['polymer', 'polymerisation', 'addition polymer', 'condensation polymer', 'molecular weight', 'industrial chemistry'],
                'content' => <<<MD
# Polymer Synthesis: Addition vs Condensation Polymerisation

The two principal routes to synthetic polymers differ fundamentally in mechanism, kinetics, and the resulting molecular weight distribution.

## Addition (Chain-Growth) Polymerisation

**Mechanism**: Reactive intermediate (radical, cation, anion, or metal centre) adds monomer units one at a time to a growing chain end. Monomer is consumed rapidly; polymer forms throughout the reaction.

### Free Radical Polymerisation
1. **Initiation**: Thermal or UV decomposition of initiator (e.g., AIBN, benzoyl peroxide) → radicals.
2. **Propagation**: Rapid sequential monomer addition (k_p ~10³ L/mol·s).
3. **Termination**: Combination (radical + radical → stable bond) or disproportionation.

**Molecular weight**: Kinetic chain length (ν) ∝ [M] / [I]^½. Increasing initiator concentration → shorter chains → lower M_n.

**Dispersity** (Đ = M_w / M_n): Typically 1.5–2.0 for conventional free radical polymerisation.

**Controlled radical methods**:
- **ATRP** (Atom Transfer Radical Polymerisation): Cu(I)/ligand activator; enables low Đ (~1.1) and block copolymers.
- **RAFT** (Reversible Addition-Fragmentation chain Transfer): Thiocarbonylthio agent; mild conditions; Đ ~1.1.
- **NMP** (Nitroxide-Mediated Polymerisation): Thermal; no metal catalyst.

### Industrial Addition Polymers

| Polymer | Monomer | Initiator / Catalyst | Applications |
|---------|---------|---------------------|--------------|
| Polyethylene (LDPE) | Ethylene | Peroxide (radical) | Film, packaging |
| Polyethylene (HDPE/LLDPE) | Ethylene | Ziegler–Natta / metallocene | Pipe, containers |
| Polypropylene | Propylene | Metallocene | Automotive, textiles |
| PVC | Vinyl chloride | Peroxide | Pipe, cable insulation |
| Polystyrene | Styrene | Peroxide or thermal | Packaging foam, appliances |
| PTFE | Tetrafluoroethylene | Radical | Non-stick, seals |

## Condensation (Step-Growth) Polymerisation

**Mechanism**: Bifunctional monomers react stepwise; any two molecules (monomer, dimer, oligomer, polymer) can react. Small molecule by-product is released (H₂O, HCl, CH₃OH).

**Key characteristic**: High molecular weight is only achieved at very high conversion (>99%). Carothers equation: X_n = 1 / (1 − p), where p is fractional conversion of functional groups.

**Molecular weight distribution**: Đ → 2.0 at high conversion (most probable distribution).

### Industrial Condensation Polymers

| Polymer | Monomers | Reaction | Applications |
|---------|----------|----------|--------------|
| Nylon 6,6 | Adipic acid + hexamethylenediamine | Amide bond | Textiles, engineering plastics |
| PET | Ethylene glycol + terephthalic acid | Ester bond | Bottles, fibres, film |
| Polycarbonate | Bisphenol A + phosgene (or DMC) | Carbonate bond | Optical media, glazing |
| Epoxy resin | DGEBA + curing agent | Epoxide ring opening | Adhesives, composites |

## Choosing the Route

| Factor | Addition | Condensation |
|--------|----------|--------------|
| Functional group requirement | One unsaturated bond | Two functional groups per molecule |
| Molecular weight control | Via [I]:[M] ratio, CRP methods | Requires >99% conversion |
| By-products | None (or inert) | H₂O, HCl must be removed |
| Copolymers | Easily controlled via comonomer ratio | Requires matching reactivity ratios |
| Reaction rate | Fast (seconds to hours) | Slow (hours to days) |
MD,
            ],
        ];

        foreach ($entries as $entry) {
            BasicKnowledgeTrend::firstOrCreate(
                ['title' => $entry['title']],
                array_merge($entry, ['category_id' => $category->id, 'status' => 'published'])
            );
        }
    }
}
