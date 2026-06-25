<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class VRFBBuildPreparationSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'science')->first();

        if (!$category) {
            $this->command->warn('Science category not found. Run DatabaseSeeder first.');
            return;
        }

        BasicKnowledgeTrend::updateOrCreate(
            ['title' => 'What Do You Need to Build a Vanadium Redox Flow Battery (VRFB)? A Complete Preparation Guide'],
            [
                'category_id' => $category->id,
                'status'      => 'published',
                'tags'        => [
                    'VRFB', 'vanadium battery', 'build VRFB', 'battery construction',
                    'electrolyte preparation', 'stack assembly', 'materials list',
                    'cell stack', 'ion exchange membrane', 'graphite felt', 'bipolar plate',
                    'vanadium electrolyte', 'energy storage', 'DIY battery', 'engineering guide',
                ],
                'summary'     => 'Building a vanadium redox flow battery (VRFB) — whether a laboratory prototype, a pilot-scale system, or a commercial installation — requires careful preparation across five domains: electrochemical components, electrolyte chemistry, balance-of-plant (fluid handling), power electronics, and safety infrastructure. This comprehensive guide details every material, specification, tool, and process step needed, from raw materials to a functioning system, with guidance for both small-scale research builds and larger commercial deployments.',
                'content'     => <<<'MD'
## Overview: What a VRFB Actually Consists Of

Before listing what to prepare, it helps to understand the physical structure of a VRFB system. It has three major subsystems:

**1. The Electrochemical Stack** — where electrical energy is converted to/from chemical energy. Composed of stacked electrochemical cells, each containing bipolar plates, cell frames, electrodes (graphite felt), and an ion-exchange membrane.

**2. The Electrolyte System** — where energy is stored. Composed of two external tanks (positive and negative), circulation pumps, piping, and the vanadium electrolyte itself.

**3. The Balance of Plant (BOP) and Power Electronics** — connects the stack to the grid or load. Includes sensors, valves, heat exchangers, a battery management system (BMS), and a bidirectional DC–AC inverter.

Everything you need to prepare falls into one of these three subsystems. This guide covers each in full detail.

---

## SECTION A: ELECTROCHEMICAL STACK COMPONENTS

The stack is the most technically demanding part to source and assemble. Each cell within the stack has the same layered structure:

```
[End Plate] → [Current Collector] → [Bipolar Plate] → [Cell Frame] →
[Electrode (graphite felt)] → [Membrane] → [Electrode (graphite felt)] →
[Cell Frame] → [Bipolar Plate] → ... → [Current Collector] → [End Plate]
```

### A1. Ion-Exchange Membrane

**Function:** Separates positive and negative electrolytes while allowing proton (H⁺) transport for charge balance. The most critical and expensive single component.

**Standard specification:**
- Material: Nafion 117 (DuPont/Chemours) — perfluorosulfonic acid ionomer
- Thickness: 183 µm (0.183 mm)
- Proton conductivity: ~90 mS/cm in H₂SO₄
- Area resistance: ~0.2 Ω·cm²
- Chemical stability: excellent in H₂SO₄ up to 5 M; resistant to V(V) oxidation
- Form: flat sheet; comes in rolls (typically 305 mm wide) or pre-cut panels

**Alternatives for lower cost:**
- Nafion 115 (127 µm thick) — lower resistance but higher vanadium crossover; suitable for prototypes where lifetime is not critical
- Nafion 212 (51 µm) — very low resistance; higher crossover; research use only
- SPEEK membranes — significantly cheaper (USD 30–80/m² vs. USD 500–800/m² for Nafion); lower efficiency but acceptable for long-duration systems; available from Fumatech (Germany) as fumapem® series

**Quantity calculation:**
- For a single cell with 200 cm² active area: one piece 200 cm² + ~20% margin for sealing → cut to ~16 cm × 16 cm
- For a 10-cell stack with 200 cm² cells: 10 membranes
- Pre-treatment required: soak Nafion in 3% H₂O₂ at 80 °C for 1 h → rinse in DI water → soak in 0.5 M H₂SO₄ at 80 °C for 1 h → store in 0.5 M H₂SO₄ until use. This removes organic contaminants and fully protonates the membrane.

**Where to source:** Chemours (USA/global), Fuel Cell Store (USA, small quantities), Sigma-Aldrich (research scale), Ion Power (USA/Europe).

---

### A2. Electrodes (Graphite Felt)

**Function:** Porous current-collecting electrodes where vanadium ion oxidation/reduction reactions occur. Must be electrically conductive, chemically stable, and provide high surface area.

**Standard specification:**
- Material: PAN-based (polyacrylonitrile) graphite felt
- Thickness: 3–6 mm (compressed to 40–60% during assembly, i.e., ~1.5–3 mm in-cell)
- Fibre diameter: 10–15 µm
- Porosity: 90–95%
- BET surface area: 0.3–1.0 m²/g (raw); 1–3 m²/g after activation
- Electrical resistivity (through-plane): < 100 mΩ·cm
- Chemical stability: excellent in H₂SO₄ up to 5 M and all four vanadium oxidation states

**Common commercial sources:**
- SGL Carbon (Germany) — SIGRACELL® GFD series (GFD 3, GFD 4.6, GFD 6 EA); "EA" suffix = pre-activated (thermally oxidised by manufacturer)
- Toray Industries (Japan) — TGP-H series (originally for fuel cells; adapted for VRFB)
- Kureha Corporation (Japan) — KUREHA™ graphite felt
- Liaoning Jingu Carbon (China) — lower-cost alternative widely used in Chinese VRFB industry

**Activation (mandatory if not pre-activated):**
Raw graphite felt is hydrophobic and electrochemically sluggish. Activation introduces oxygen-functional groups (–OH, –COOH, C=O) on fibre surfaces:

*Thermal activation (recommended):*
1. Cut felt to cell dimensions + 2–3 mm margin
2. Place in muffle furnace on alumina tray
3. Heat to 400°C in air at 5°C/min ramp
4. Hold at 400°C for 30 minutes
5. Cool to room temperature under air flow
6. Verify: activated felt wets immediately with water (drops absorbed in < 2 seconds); un-activated felt beads water

*Chemical activation (alternative for higher activity):*
1. Immerse felt in 98% H₂SO₄ : 65% HNO₃ = 3:1 by volume for 8 hours at room temperature
2. Rinse thoroughly with DI water (6 × rinse cycles until pH neutral)
3. Dry at 60°C for 12 hours
4. Handle with acid-resistant gloves throughout

**Bismuth modification (for negative electrode — highly recommended):**
Adds Bi nanoparticles that dramatically improve V²⁺/V³⁺ kinetics and suppress H₂ evolution:
1. Prepare 0.01–0.05 M Bi(NO₃)₃ in 2 M H₂SO₄
2. Soak activated graphite felt in solution for 2 hours
3. Apply gentle current (5–10 mA/cm²) for 30 min to electrodeposit Bi⁰ nanoparticles
   OR: immerse activated felt in solution at 60°C for 4 hours (chemical deposition)
4. Rinse with DI water; dry at 60°C

**Quantity:** Two electrodes per cell (one positive, one negative). For a 10-cell stack, 200 cm² each → 20 pieces graphite felt cut to ~14 cm × 15 cm (allowing for frame seal overlap).

---

### A3. Bipolar Plates (BPP)

**Function:** Electrically connects adjacent cells in the stack (conducts electrons from one cell's positive electrode to the next cell's negative electrode); separates positive electrolyte of one cell from negative electrolyte of the next; contains flow field channels that distribute electrolyte across the electrode.

**Standard specification:**
- Material: Graphite/polypropylene composite (60–80 wt% graphite, remainder PP binder)
- Thickness: 3–6 mm
- Through-plane electrical conductivity: > 200 S/cm (resistivity < 5 mΩ·cm²)
- Chemical stability: excellent in H₂SO₄ and vanadium solution
- Flow field: serpentine channels (1.5–2 mm wide × 1–2 mm deep), machined or compression-moulded into both faces

**Machining flow channels (if purchasing flat BPP stock):**
Flat BPP stock is commercially available (Eisenhuth GmbH, Germany; Shenzhen JCHX Mining Management, China). CNC machining serpentine channels requires:
- CNC milling machine with 1.5–2 mm end mill carbide bit
- CAD design of serpentine flow pattern (single-serpentine for cells < 400 cm²; multi-pass serpentine for larger)
- Carbon/graphite composites are brittle — use low feed rates, sharp tooling, air cooling (no cutting fluid)
- Surface finish: Ra < 1.6 µm on electrode contact surface (improves contact resistance)

**Alternative: purchase pre-machined BPPs:**
- Eisenhuth GmbH (Germany) — custom-machined graphite BPP to any specification
- Schunk Carbon Group (Germany)
- GrafTech International (USA)
- Various Chinese suppliers for standard sizes (200 cm², 400 cm², 600 cm²)

**Quantity:** N+1 bipolar plates for an N-cell stack (the end cells use half-plates — positive face only). For 10-cell stack: 9 full bipolar plates + 2 end half-plates.

---

### A4. Cell Frames

**Function:** Defines the cell volume, holds the electrode and membrane in position, seals against electrolyte leakage, and provides inlet/outlet manifolds for electrolyte distribution.

**Standard specification:**
- Material: Polyvinyl chloride (PVC) — good chemical resistance, easy to machine, low cost; OR polypropylene (PP); OR PVDF (superior chemical resistance; higher cost)
- Frame thickness: 4–8 mm
- Window opening: defines active electrode area (e.g., 140 mm × 140 mm = 196 cm²)
- Integrated manifold: distribution channels machined into frame to spread electrolyte from inlet port across the electrode width
- Gasket grooves: for O-ring or flat gasket sealing between frame layers

**Fabrication options:**
1. **CNC machining from plastic sheet** (best for prototypes): PP or PVC sheet (10–15 mm thick) machined on CNC router. Requires: CNC router, 6–10 mm end mill bits, coolant.
2. **3D printing** (rapid prototyping): FDM printing in PETG or PP (PP is chemically resistant but difficult to print; PETG is easier but swells slightly in concentrated H₂SO₄ — only use for short-term testing). Print at 0.2 mm layer height, 4 perimeters for liquid-tight walls.
3. **Injection moulding** (commercial production): Upfront tooling cost USD 5,000–30,000 per mould; justified above ~500 units.

**Sealing method:**
- **Flat EPDM (ethylene propylene diene monomer) gaskets** — cheapest; must be cut to frame shape; suitable for pressures < 1 bar
- **Silicone O-rings in machined grooves** — preferred for pressures 1–3 bar; more reliable long-term seal
- **PTFE gasket tape** — wrapped around electrode perimeter as a compressible seal; used in many commercial systems
- Avoid: natural rubber (attacked by H₂SO₄), NBR nitrile (moderate resistance but degrades in V(V)), standard polyurethane (attacked by H₂SO₄)

**Quantity:** 2 frames per cell (one for each half-cell). For 10-cell stack: 20 frames.

---

### A5. End Plates and Current Collectors

**End plates (2 required, one at each end of the stack):**
- Function: apply uniform clamping force across the stack; provide structural rigidity
- Material: aluminium alloy (6061-T6) or stainless steel 316L
- Thickness: 15–25 mm (must resist bending under clamping load)
- Surface: must be electrically isolated from the current collector (insulating pad between end plate and current collector)
- Bolt pattern: 4–8 through-bolts (M8–M12 stainless steel) or tie-rods with compression springs for uniform and adjustable clamping pressure

**Current collectors (2 required, positive and negative terminals):**
- Function: collect electrons from the outermost electrode and deliver them to the external circuit
- Material: gold-plated copper (research) or graphite plate (commercial — avoids corrosion in H₂SO₄ atmosphere)
- Thickness: 3–5 mm
- Terminal connection: threaded stud or bolt hole for cable lug attachment

**Clamping pressure:** Target 0.5–1.5 MPa on electrode face (critical parameter — too low = high contact resistance; too high = electrode compression blocks flow). Use torque wrench on bolts + calculate from bolt pattern and elastic modulus of stack.

---

### A6. Sealing and Assembly Hardware

| Item | Specification | Quantity (10-cell stack) |
|---|---|---|
| EPDM or PTFE flat gaskets | Cut to frame profile, 1–2 mm thick | 20 |
| M8 × 150 mm stainless steel tie-rods | Grade 316 SS, with nuts and washers | 8 rods |
| Stainless steel springs (optional) | For constant-force clamping | 8 |
| Torque wrench | 5–50 Nm range | 1 |
| Electrically insulating end-plate pads | HDPE or PTFE sheet, 5 mm | 2 |

---

## SECTION B: ELECTROLYTE PREPARATION

The vanadium electrolyte is the energy storage medium. Its correct preparation, specification, and volume calculation are critical.

### B1. Understanding the Electrolyte

A VRFB uses two separate vanadium electrolyte solutions:

**Positive electrolyte (catholyte):**
- Charged: VO₂⁺ (V(V), vanadium pentavalent) — yellow-orange
- Discharged: VO²⁺ (V(IV), vanadyl) — blue
- Acid: 2–3 M H₂SO₄

**Negative electrolyte (anolyte):**
- Charged: V²⁺ (V(II)) — violet
- Discharged: V³⁺ (V(III)) — green
- Acid: 2–3 M H₂SO₄

**Standard electrolyte specification:**
- Vanadium concentration: 1.5–1.7 M (most common commercial specification)
- Sulfuric acid concentration: 2.0–3.0 M total H₂SO₄
- pH: 0–1 (strongly acidic — handle with appropriate PPE)
- Density: ~1.35–1.40 g/mL
- Nominal energy density: ~25 Wh/L (at 1.6 M, 100% DoD, 1.26 V avg)
- Colour: both electrolytes start as V(IV) — blue — before the first charge cycle splits them

### B2. Raw Materials for Electrolyte Preparation

**Option 1: Prepare from vanadium pentoxide (V₂O₅) — most common**

*Materials required:*

| Chemical | Specification | Amount for 10 L electrolyte at 1.6 M |
|---|---|---|
| Vanadium pentoxide (V₂O₅) | ≥ 98% purity, powder | 1454 g (MW = 181.88 g/mol; 1.6 mol/L × 10 L = 16 mol; 16 × 181.88/2 = 1455 g) |
| Sulfuric acid (H₂SO₄) | 95–98% concentration, reagent or technical grade | ~1100 mL (to achieve 2.5 M final H₂SO₄ in 10 L) |
| Deionised water | Resistivity > 1 MΩ·cm (type 2 DI) | ~8.5 L |
| Oxalic acid (C₂H₂O₄) | Reagent grade | ~200 g (as reductant to convert V(V) → V(IV)) |

*Preparation procedure:*
1. To a 15 L HDPE or PP vessel (acid-resistant), add 7 L DI water
2. Slowly add H₂SO₄ to water (NEVER reverse — exothermic; add acid to water) with stirring
3. Add V₂O₅ powder slowly while stirring; V₂O₅ dissolves in H₂SO₄ to form VO₂⁺ (V(V)): V₂O₅ + H₂SO₄ → 2 VO₂⁺ + SO₄²⁻ + H₂O
4. Heat solution to 60–70°C with stirring until fully dissolved (1–3 hours for 1 kg batches)
5. Add oxalic acid (C₂H₂O₄) slowly — this reduces V(V) → V(IV): V₂O₅ + H₂C₂O₄ → 2 VOSO₄ + CO₂ + H₂O. Solution turns from yellow-orange to deep blue. Add only enough oxalic acid to achieve V(IV) — stop when orange colour is fully gone.
6. Make up volume to 10 L with DI water; measure vanadium concentration by ICP-OES or titration
7. This gives 10 L of 1.6 M V(IV) electrolyte — identical composition in both tanks before first charge

**Option 2: Purchase ready-made electrolyte**
Commercial VRFB electrolyte (vanadyl sulfate solution, 1.6 M V in 2.5 M H₂SO₄) is available from:
- US Vanadium (Hot Springs, Arkansas, USA)
- Largo Clean Energy (USA — uses vanadium from own mine in Brazil)
- REVATECH (Belgium) — recycled vanadium electrolyte
- Australian Vanadium Ltd (VSUN) — Australian production
- Multiple Chinese suppliers (Pangang Group, CITIC Guoan, Sichuan Zhuo'nuo)

**Advantage of purchased electrolyte:** Certified concentration, purity, and correct H₂SO₄ balance. Saves significant preparation time and equipment. For pilot/commercial systems, this is almost always preferred.

### B3. Electrolyte Volume Calculation

The electrolyte volume determines the energy capacity of the system.

**Formula:**
Energy (Wh) = C_vanadium (mol/L) × Volume (L) × n_e × F × V_avg / 3600

Where:
- C_vanadium = molar concentration (1.6 mol/L typical)
- Volume = volume of ONE tank (positive OR negative; both tanks equal)
- n_e = electrons transferred per vanadium ion = 1
- F = Faraday constant = 96,485 C/mol
- V_avg = average cell voltage during discharge (~1.25 V for a single cell; multiply by cells-in-series for stack voltage)
- 3600 = conversion from joules to watt-hours

**Simplified practical formula:**
**Energy per tank (Wh) ≈ 26.8 × C (mol/L) × V_tank (L)**

*Example:* 1.6 M vanadium, 100 L per tank:
Energy = 26.8 × 1.6 × 100 = **4,288 Wh ≈ 4.3 kWh** (at 100% DoD; practical DoD ~85% = ~3.6 kWh usable)

**For a 10 kWh system at 1.6 M:** Volume per tank ≈ 10,000 / (26.8 × 1.6) ≈ 233 L per tank → round to 250 L each for safety margin.

### B4. Electrolyte Purity Requirements

Contaminants destroy VRFB performance. Critical impurity limits:

| Impurity | Maximum allowable | Effect if exceeded |
|---|---|---|
| Fe (iron) | < 100 ppm | Irreversibly reduces V(V) → V(IV); capacity fade |
| Cu (copper) | < 10 ppm | Plates onto negative electrode; blocks V²⁺ reaction |
| Cl⁻ (chloride) | < 100 ppm (unless mixed-acid design) | Corrodes bipolar plates; generates Cl₂ gas at positive electrode |
| Mn (manganese) | < 10 ppm | Oxidises to MnO₂ which deposits on electrodes |
| Ni (nickel) | < 50 ppm | Reduces electrode activity |
| Si (silica) | < 50 ppm | Clogs porous electrode |

Specification-grade V₂O₅ (98% purity) typically meets these limits. Verify with ICP-OES analysis before use.

---

## SECTION C: ELECTROLYTE TANKS AND FLUID HANDLING

### C1. Electrolyte Tanks

**Material requirements:**
- Must resist: 2–3 M H₂SO₄, V(V) (strongly oxidising), V(II) (strongly reducing), temperatures 10–40°C
- Acceptable materials: HDPE (high-density polyethylene) — excellent chemical resistance, low cost; PP (polypropylene) — similar to HDPE; CPVC (chlorinated PVC) — better structural strength; fibreglass (FRP) with PVDF or PP liner

**Not acceptable:** standard stainless steel (corrodes in H₂SO₄), aluminium (dissolves), carbon steel, standard PVC (insufficient V(V) resistance long-term), rubber-lined tanks (rubber degrades)

**Sizing and features:**
- Volume: electrolyte volume + 20% headspace for thermal expansion and level variation
- Lid/vent: tanks must be sealed with a small vent to prevent pressure build-up (H₂ evolution at negative electrode during overcharge) with flame arrestor on vent
- Level sensor: float switch or ultrasonic level sensor to detect leaks and electrolyte imbalance
- Temperature sensor: PT100 or thermocouple; electrolyte must not exceed 40°C (V(V) precipitation)
- Drain valve: PP or PVDF ball valve at tank bottom for electrolyte removal
- Inspection port: 100–150 mm diameter cleanout port

**For a 10 kWh system (250 L per tank):** Two 300 L HDPE tanks (allowing 20% headspace). Standard industrial HDPE tanks in 250–500 L range are commercially available at low cost (USD 150–400 each).

### C2. Circulation Pumps

**Function:** Circulate electrolyte from tanks through the stack at controlled flow rate.

**Material requirements (wetted parts):**
- Must resist H₂SO₄ and vanadium electrolyte
- Acceptable: PVDF (polyvinylidene fluoride) — best chemical resistance; PP — lower cost; PTFE-lined stainless steel

**Type:**
- **Centrifugal pump** (most common for > 50 L/min): magnetically coupled (no shaft seal, zero leakage risk) preferred over mechanical seal type. Brands: Iwaki (Japan/USA), Grundfos CM-A series in PVDF, KNF (Germany).
- **Peristaltic pump** (lab scale / < 10 L/min): rollers compress flexible tubing; fluid never contacts pump mechanism; easy to calibrate flow rate. Brands: Masterflex (Cole-Parmer), Watson-Marlow. Use Masterflex Norprene or PVDF tubing.

**Flow rate requirement:**
- Optimal stoichiometric flow: 5–10 mL/min per cm² of active electrode area at rated current density
- For 200 cm² active area at 80 mA/cm² (16 A total): ~1000–2000 mL/min (1–2 L/min) per half-cell
- Pump head pressure: typically 0.5–2 bar (accounts for piping pressure drop, membrane pressure differential, electrode compression)
- **One pump per electrolyte tank** (two pumps total for the system)

### C3. Piping and Fittings

All piping and fittings in contact with electrolyte must be chemically resistant:

| Component | Acceptable material | Notes |
|---|---|---|
| Pipe / tubing | PVDF, PP, PVC (schedule 80 or above) | Avoid standard PVC for V(V) long-term |
| Fittings (tee, elbow, union) | PVDF or PP | Compression fittings preferred over threaded for < DN25 |
| Valves | PVDF or PP ball valves; PTFE seats | Avoid brass, bronze |
| Flexible hose | PVDF-lined braided hose; or Norprene tubing | At pump connections for vibration isolation |
| Pressure gauge | PVDF wetted parts; glycerine-filled dial | One per electrolyte circuit |
| Flow meter | Electromagnetic (inline PVDF body) or rotameter (borosilicate glass with PVDF body) | One per electrolyte circuit |

**Pipe sizing (for 10 kWh system):** DN15 (½") or DN20 (¾") pipe is adequate for flow rates up to 10 L/min. Use larger DN25 (1") for lower pressure drop at higher flow rates.

### C4. Heat Exchanger (Optional but Recommended)

At high charge/discharge currents, the stack generates heat that can raise electrolyte temperature above 40°C (causing V(V) precipitation). A compact plate heat exchanger (PVDF or titanium plates, PP frame) with cooling water circuit maintains electrolyte at 20–35°C.

For small systems (< 10 kW), passive cooling via tank surface area may be sufficient. For > 10 kW, active cooling is recommended.

---

## SECTION D: POWER ELECTRONICS AND CONTROL SYSTEMS

### D1. Bidirectional DC–AC Inverter

**Function:** Converts DC from the VRFB stack to AC grid power (discharge) and AC grid power to DC for charging.

**Specifications required:**
- Voltage range: must match stack DC voltage (N cells × 1.0–1.6 V per cell)
  - 10-cell stack: 10–16 V DC → need low-voltage inverter (uncommon; most inverters target 48–800 V DC)
  - 40-cell stack: 40–64 V DC → compatible with 48 V nominal inverter
  - 100-cell stack: 100–160 V DC → compatible with standard string inverter range
- Bidirectional operation: must support both grid feed-in and grid charging modes
- Efficiency: > 95% at rated power
- Communication: Modbus RTU or CAN bus for BMS integration

**Common commercial inverters for VRFB integration:**
- SMA Sunny Island (48 V DC, off-grid with AC coupling): suitable for small VRFB
- Victron Quattro (48 V DC, bidirectional, 3–15 kVA range): popular for small VRFB prototypes
- Parker / Ingeteam / Princeton Power: industrial bidirectional inverters for larger VRFB systems
- Custom power electronics: many VRFB manufacturers (Invinity, StorTera, Rongke) build proprietary inverters matched to their stack voltage

### D2. Battery Management System (BMS)

The VRFB BMS is simpler than a LIB BMS (no cell-level monitoring required) but must manage:

**Core BMS functions:**
1. **State of Charge (SoC) estimation** — from stack OCV, UV-Vis spectroscopy, or coulomb counting
2. **Overcharge / over-discharge protection** — cut-off current when SoC > 95% (charge) or < 5% (discharge)
3. **Temperature monitoring** — electrolyte temperature in both tanks (2 × PT100 sensors); stack temperature (2–4 × thermocouple); pump motor temperature
4. **Pump control** — start/stop pumps; variable speed control (VFD if variable flow desired)
5. **Fault detection and alarming** — electrolyte leak detection (conductivity sensor in containment sump), H₂ gas detection, pump failure, flow rate low alarm
6. **Communication** — Modbus TCP or RS485 to SCADA or energy management system (EMS)
7. **Electrolyte rebalancing** — periodic equalisation charge to correct capacity imbalance

**BMS hardware:**
- PLC (programmable logic controller): Siemens S7-1200, Allen-Bradley Micro820, or Arduino Mega / Raspberry Pi for lab-scale systems
- Analogue input modules for sensor data
- Digital output modules for pump, valve, and inverter control
- HMI (human-machine interface): touchscreen panel or PC-based SCADA

**Sensors required (minimum set for a 10 kWh system):**

| Sensor | Quantity | Placement | Purpose |
|---|---|---|---|
| PT100 temperature probe | 4 | Positive tank, negative tank, stack inlet, stack outlet | Electrolyte temperature monitoring |
| Pressure transducer (0–3 bar) | 2 | Each pump outlet | Flow resistance monitoring |
| Electromagnetic flow meter | 2 | Each electrolyte circuit | Flow rate verification |
| Conductivity probe | 2 | Containment sump (both tanks) | Leak detection |
| H₂ gas detector | 1–2 | Stack enclosure / negative tank vent | H₂ safety monitoring |
| Current transducer (Hall-effect) | 1 | DC bus | Coulomb counting for SoC |
| Voltage transducer | 1 | Stack terminals | SoC estimation via OCV |
| Reference cell (optional) | 1 | External to main stack | Accurate OCV-based SoC |

---

## SECTION E: SAFETY INFRASTRUCTURE

Building a VRFB involves working with concentrated sulfuric acid and toxic vanadium compounds. Safety preparation is not optional.

### E1. Personal Protective Equipment (PPE)

| PPE Item | Specification | Use |
|---|---|---|
| Chemical-resistant gloves | Nitrile (disposable, two pairs worn simultaneously) or neoprene | All electrolyte handling |
| Safety goggles | Chemical splash-rated (not just safety glasses) | All electrolyte handling |
| Face shield | Full-face polycarbonate | Mixing acid, electrolyte transfers |
| Chemical-resistant apron | PP or PVDF-coated nylon | All electrolyte work |
| Chemical-resistant boots | PP or PVC overboots | Electrolyte preparation and spill cleanup |
| Respirator | P100 + OV/AG cartridge | Acid mixing, roasting graphite felt (if thermal activation performed in enclosed space) |

### E2. Spill Containment

All electrolyte tanks, piping, and the stack must be installed within a **secondary containment structure** (bund or drip tray) sized to hold 110% of the total electrolyte volume (both tanks combined):

- Material: HDPE-lined concrete, PP sheet, or polyurea-coated concrete
- Slope to a collection sump with PVDF pump for recovery
- For a 10 kWh system (2 × 250 L = 500 L electrolyte): bund must hold ≥ 550 L

### E3. Emergency Equipment

| Equipment | Location | Purpose |
|---|---|---|
| Emergency eyewash station | Within 10 seconds travel (≤ 10 m) | Immediate eye flush after acid splash |
| Safety shower | Within 10 seconds travel | Full body decontamination |
| Acid neutralisation kit | Adjacent to containment area | Sodium bicarbonate (NaHCO₃) powder for spill neutralisation (50 kg for large systems) |
| First aid kit | Accessible | Include burn treatment dressings |
| H₂SO₄ MSDS / SDS | Prominently posted | Emergency response procedures |
| Vanadium compounds SDS | Prominently posted | Toxicology and first-aid info |
| Fire extinguisher (CO₂ or dry powder) | Adjacent to electrical panels | Electrical fire (do NOT use water on electrical fires) |

### E4. Ventilation

Hydrogen gas (H₂) evolves at the negative electrode during overcharge and at V²⁺ electrolyte surfaces. H₂ forms explosive mixtures with air at 4–75 vol% H₂.

**Requirements:**
- Stack enclosure: forced ventilation providing minimum 6 air changes per hour
- Tank enclosure: passive ventilation to atmosphere with ATEX-rated (explosion-proof) vent fans
- H₂ gas detectors (set alarm at 10% of lower explosive limit = 0.4 vol% H₂) with automatic pump shutdown on H₂ alarm
- No ignition sources (open flames, sparks, non-ATEX electrical equipment) within stack/tank enclosure

### E5. Chemical Waste Disposal

- Spent vanadium electrolyte: classified as hazardous waste in most jurisdictions; must be managed by licensed hazardous waste contractor OR shipped to vanadium electrolyte recycler
- Sulfuric acid rinse water: neutralise to pH 6–9 with NaHCO₃ before drain disposal
- Used Nafion membrane: PFAS-containing material; classified as hazardous waste in EU (REACH SVHC) and some US states; dispose via licensed contractor

---

## SECTION F: TOOLS AND EQUIPMENT FOR ASSEMBLY

### F1. Workshop Tools

| Tool | Purpose |
|---|---|
| Torque wrench (5–50 Nm) | Stack bolt clamping to specified pressure |
| Vernier caliper / micrometer | Measuring component dimensions and stack compression |
| Utility knife / scissors | Cutting membrane and gasket material |
| Flat-jaw pliers | Tightening fittings |
| Peristaltic or syringe pump | Controlled electrolyte filling during commissioning |
| pH meter (pH 0–4 range) | Verifying electrolyte acid concentration |
| Multimeter (DC, 0–200 V, 0–100 A) | Stack voltage and current monitoring |
| Digital thermometer (with K-type thermocouple) | Temperature measurement during activation, electrolyte preparation |
| Magnetic stirrer / hot plate | Electrolyte preparation |
| Chemical balance (0.1 g resolution) | Weighing reagents |

### F2. Electrochemical Testing Equipment

For laboratory and pilot systems, basic electrochemical characterisation is essential:

| Equipment | Specification | Purpose |
|---|---|---|
| Potentiostat / galvanostat | Bi-directional, ±50 V / ±10 A (lab scale) | Single-cell charge-discharge cycling; EIS measurements |
| Electronic load (programmable) | 0–50 V / 0–100 A | Constant-current or constant-power discharge testing |
| Data acquisition system (DAQ) | 16+ analog channels, 1 Hz minimum | Logging voltage, current, temperature, flow during testing |
| Peristaltic pump (2 channels) | 10–2000 mL/min, PVDF head | Electrolyte circulation in lab-scale system |
| UV-Vis spectrophotometer (optional) | 300–900 nm range | Vanadium species concentration measurement (SoC) |

---

## SECTION G: REGULATORY AND SITE REQUIREMENTS

### G1. Permits Required (Typical)

| Permit / Approval | Issuing authority | Relevance |
|---|---|---|
| Building / construction permit | Local council / municipality | Any permanent structure for tanks or stack |
| Dangerous goods storage licence | Fire authority or HSE | Sulfuric acid (Class 8 corrosive); quantity-dependent thresholds |
| Environmental impact assessment | Environmental agency | Large systems; arsenic/vanadium in electrolyte is a regulated substance in some jurisdictions |
| Electrical installation certificate | Licensed electrician / utility | Grid connection; DC-AC inverter installation |
| Grid connection agreement | Distribution network operator (DNO) | For grid-tied systems |

### G2. Standards Compliance

| Standard | Scope |
|---|---|
| IEC 62932 (flow battery systems) | Safety requirements for flow batteries for stationary applications |
| IEC 62619 | Safety for stationary energy storage (general) |
| UL 9540 | Standard for energy storage systems (USA) |
| NFPA 855 | Standard for installation of stationary energy storage systems (USA) |
| IEC 60364 | Electrical installation wiring rules |
| REACH / RoHS | EU chemical substance regulations (vanadium pentoxide is SVHC candidate) |

---

## SECTION H: COMPLETE BILL OF MATERIALS — EXAMPLE 10-CELL STACK, ~1 kWh LABORATORY VRFB

| Item | Specification | Qty | Approx. Unit Cost (USD) | Approx. Total |
|---|---|---|---|---|
| Nafion 117 membrane | 14 cm × 15 cm | 10 | USD 25–40 each | USD 250–400 |
| Graphite felt (activated) | SGL GFD 3 EA, 14 × 15 cm | 20 | USD 8–15 each | USD 160–300 |
| Bipolar plate (graphite/PP, 200 cm²) | Pre-machined serpentine, 5 mm | 9 | USD 40–80 each | USD 360–720 |
| Half bipolar plate (end cells) | As above, one face only | 2 | USD 25–50 each | USD 50–100 |
| Cell frame (PP, machined) | 200 cm² window, 6 mm thick | 20 | USD 15–30 each | USD 300–600 |
| EPDM flat gaskets | Custom cut, 1.5 mm | 20 | USD 3–8 each | USD 60–160 |
| End plates (aluminium) | 200 mm × 200 mm × 20 mm | 2 | USD 30–60 each | USD 60–120 |
| Current collectors (graphite) | 200 cm², 5 mm | 2 | USD 20–40 each | USD 40–80 |
| M8 × 150 mm SS tie-rods + nuts | Grade 316, 8-bolt pattern | 8 sets | USD 5 each | USD 40 |
| HDPE electrolyte tanks | 20 L (lab scale) | 2 | USD 30–50 each | USD 60–100 |
| Peristaltic pump head (PVDF) | Watson-Marlow 520S | 2 | USD 300–500 each | USD 600–1000 |
| PVDF tubing | 10 mm ID, 5 m | 2 | USD 20–40/m | USD 200–400 |
| PVDF ball valves | DN10, ½" | 4 | USD 15–30 each | USD 60–120 |
| Rotameter flow meter | 0–2 L/min, PP body | 2 | USD 40–80 each | USD 80–160 |
| Pressure gauge (PVDF) | 0–3 bar | 2 | USD 25–50 each | USD 50–100 |
| V(IV) electrolyte (1.6 M, 2.5 M H₂SO₄) | Purchased ready-made | 20 L | USD 30–60/L | USD 600–1200 |
| Potentiostat / galvanostat | ±10 A, ±50 V | 1 | USD 2000–8000 | USD 2000–8000 |
| **TOTAL ESTIMATED BUILD COST** | | | | **~USD 5,000–13,000** |

*Note: Costs vary significantly by region, supplier, and whether commercial or academic pricing applies. The potentiostat dominates cost for lab systems; for non-research builds, replace with a commercial DC power supply + electronic load (USD 300–600 combined).*

---

## SECTION I: STEP-BY-STEP PREPARATION SEQUENCE

**Phase 1: Design (2–4 weeks)**
1. Define target energy (kWh) and power (kW) — determines stack cell count and electrolyte volume
2. Calculate active electrode area from target current density (recommend 60–100 mA/cm² for beginners)
3. Select cell count (N = target voltage / 1.3 V per cell)
4. Draw cell frame and BPP designs; specify all dimensions
5. Plan fluid circuit (tank → pump → stack inlet → stack outlet → tank); draw P&ID diagram
6. Plan electrical circuit; specify inverter and BMS requirements
7. Prepare complete bill of materials and source all components

**Phase 2: Component Procurement (2–6 weeks)**
8. Order membranes, graphite felt, BPPs, cell frames, hardware
9. Order electrolyte (purchase ready-made) or procure V₂O₅, H₂SO₄, oxalic acid
10. Order pumps, tanks, piping, fittings, instrumentation
11. Order PPE and safety equipment

**Phase 3: Component Preparation (1–2 weeks)**
12. Activate graphite felt (thermal oxidation at 400°C or acid treatment)
13. Apply Bi modification to negative electrode felt (if selected)
14. Pre-treat Nafion membranes (H₂O₂ → rinse → H₂SO₄ → store in H₂SO₄)
15. Machine or verify fit of cell frames and BPPs
16. Cut gaskets to size
17. If preparing electrolyte from V₂O₅: dissolve, reduce, characterise (ICP-OES recommended)

**Phase 4: Stack Assembly (1–3 days)**
18. Prepare clean, flat assembly surface
19. Lay first end plate + current collector
20. Add first half-bipolar plate (positive face up)
21. Add first cell frame (positive half); insert electrode (graphite felt) into frame window
22. Lay membrane (pre-wetted in 0.5 M H₂SO₄) centrally over frame and electrode
23. Add second cell frame (negative half); insert electrode into frame window
24. Add full bipolar plate (ensure flow channels on correct faces relative to electrolyte direction)
25. Repeat steps 21–24 for each additional cell
26. Add second half-bipolar plate + current collector + end plate
27. Insert tie-rods; hand-tighten nuts; then torque progressively in cross pattern to target clamping pressure
28. Connect electrolyte inlet/outlet ports with PVDF fittings; verify all connections sealed

**Phase 5: Fluid Circuit Assembly (1 day)**
29. Install tanks in secondary containment bund
30. Connect tank → pump → stack → tank piping; install all valves and instruments
31. Pressure-test fluid circuit with DI water at 1.5× operating pressure for 30 minutes before introducing electrolyte
32. Inspect all joints for leaks; rectify any weeping joints
33. Drain and dry fluid circuit before electrolyte filling

**Phase 6: Electrical Connection (half day)**
34. Connect current collectors to inverter/power supply DC terminals
35. Install BMS sensors (temperature probes, flow meters, H₂ detector)
36. Connect BMS to pump VFDs and alarm systems
37. Test all sensor readings (verify temperature, flow, current, voltage all reading correctly)

**Phase 7: Electrolyte Filling and First Charge (1 day)**
38. Fill both tanks with V(IV) electrolyte; verify no leaks for 30 minutes
39. Start pumps at low flow rate; verify flow through stack (observe electrolyte entering and exiting stack)
40. Apply a slow first charge at C/20 rate (very slow); monitor voltage rise
   - During first charge: positive electrolyte turns from blue (V⁴⁺) to yellow-orange (V⁵⁺); negative electrolyte turns from blue (V⁴⁺) to green (V³⁺) then violet (V²⁺)
   - Monitor for voltage anomalies (shorted cells produce flat voltage profile)
   - Monitor for H₂ evolution (hydrogen smell at negative tank vent — normal in small amounts at high SoC)
41. Charge to 95% SoC; then discharge at C/10 rate; record capacity
42. Repeat 3–5 cycles; observe that capacity stabilises (activation of electrode surfaces)
43. Run at target current density; measure voltage efficiency, coulombic efficiency, energy efficiency

**Phase 8: Performance Characterisation and Optimisation**
44. Measure polarisation curves (voltage vs. current at fixed SoC) to identify limiting resistances
45. Adjust clamping pressure if contact resistance is high
46. Check for electrolyte imbalance (if positive tank becomes less blue on charge → adjust with electrochemical rebalancing)
47. Log data over 10+ cycles; verify stable capacity and efficiency
MD,
            ]
        );

        $this->command->info('Seeded 1 BasicKnowledgeTrend entry: What You Need to Build a VRFB.');
    }
}
