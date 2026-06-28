<?php

namespace Database\Seeders;

use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BusinessStrategyKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'business-strategy'],
            ['name' => 'Business & Strategy', 'sort_order' => 20]
        );

        $entries = [

            // ── 1 ──────────────────────────────────────────────────────────────
            [
                'title'   => 'Evidence-Based Strategy: Building a Business Plan Around Research Findings',
                'summary' => 'How to transform raw research output into a structured, decision-ready business strategy — covering hypothesis framing, data validation, and translating findings into actionable goals.',
                'tags'    => ['business strategy', 'research', 'evidence-based', 'planning', 'decision making'],
                'content' => <<<MD
# Evidence-Based Strategy: Building a Business Plan Around Research Findings

Most business strategies fail not from lack of ambition but from lack of evidence. An evidence-based approach treats business strategy the same way a scientist treats an experiment — with a testable hypothesis, structured data collection, and conclusions that drive action.

## Why Research Must Drive Strategy

Gut-feel decisions carry hidden risk. Research — whether market research, technical feasibility studies, or competitive intelligence — surfaces assumptions before they become expensive commitments. The key is knowing how to translate research outputs into strategic language that decision-makers can act on.

## The 5-Step Evidence-Based Strategy Framework

### Step 1: Define the Strategic Question as a Hypothesis

Before gathering data, state what you are trying to prove or disprove. A vague question produces vague strategy.

| Weak Question | Strong Hypothesis |
|---|---|
| "Is there a market for our product?" | "At least 15% of manufacturing SMEs in the target region spend >$50k/year on this problem and have no adequate solution." |
| "Should we expand?" | "Entering Market X will yield positive ROI within 18 months given our current cost base and a 5% market share target." |

A falsifiable hypothesis forces you to define what evidence would change your decision — which is the entire point.

### Step 2: Map Your Evidence Sources

| Source Type | What It Answers | Reliability |
|---|---|---|
| Primary research (surveys, interviews) | Unmet needs, willingness to pay | High — if sample is valid |
| Secondary research (reports, databases) | Market size, growth rates, competitive landscape | Medium — check recency |
| Internal data (sales, ops, financials) | What's actually working today | High |
| Expert interviews | Industry dynamics, regulatory direction | Medium — mitigate bias |
| Pilot/prototype tests | Product-market fit, pricing response | Very high — direct signal |

### Step 3: Validate or Invalidate Each Assumption

List every assumption embedded in your strategy. For each:
- What is the evidence that supports it?
- What is the evidence that challenges it?
- What would you need to see to change your position?

This is a **Strategic Assumption Map** — a living document that keeps strategy honest as new data arrives.

### Step 4: Translate Findings into Strategic Choices

Research findings are inputs; strategy is a set of choices. Apply the **5 Strategic Choice Framework**:

1. **Winning aspiration** — What does success look like in 3–5 years?
2. **Where to play** — Which markets, segments, geographies?
3. **How to win** — Cost leadership, differentiation, niche focus?
4. **Capabilities required** — What must you be best at?
5. **Management systems** — What processes and metrics govern execution?

Each choice should map back to a specific research finding. If a choice cannot be traced to evidence, it is an assumption — flag it for validation.

### Step 5: Build in Review Triggers

Strategy becomes stale when the evidence that underpinned it changes. Set explicit triggers:
- A competitor enters or exits the market
- A key assumption is validated or invalidated
- A quarterly metric deviates by more than ±20% from forecast

Review triggers prevent organisations from executing a strategy that the evidence no longer supports.

## Common Pitfalls

**Confirmation bias** — Seeking data that confirms the direction already chosen. Counter with a dedicated devil's advocate review before finalising strategy.

**Analysis paralysis** — Waiting for perfect data before deciding. Set a decision deadline and work with the best evidence available at that point.

**Treating research as a one-time event** — Strategy must be fed with continuous research, not a single upfront study.

## Summary

| Stage | Output |
|---|---|
| Hypothesis definition | Testable strategic question |
| Evidence mapping | Source inventory and reliability scores |
| Assumption validation | Strategic Assumption Map |
| Strategic choices | 5-choice framework linked to evidence |
| Review system | Trigger-based strategy refresh cadence |

Evidence-based strategy does not eliminate uncertainty — it makes uncertainty explicit, manageable, and actionable.
MD,
            ],

            // ── 2 ──────────────────────────────────────────────────────────────
            [
                'title'   => 'Active Science Information Management: Turning Knowledge Assets into Strategic Advantage',
                'summary' => 'A framework for organisations to actively manage scientific information — classifying, curating, and deploying research knowledge as a strategic asset rather than an archive.',
                'tags'    => ['science information', 'knowledge management', 'strategy', 'information assets', 'research organisations'],
                'content' => <<<MD
# Active Science Information Management: Turning Knowledge Assets into Strategic Advantage

Scientific organisations accumulate vast amounts of information — research reports, experimental data, literature reviews, patents, technical standards. Most of this is stored but not managed. Active science information management (ASIM) treats this knowledge as a live strategic asset.

## The Difference Between Storage and Management

| Passive Storage | Active Management |
|---|---|
| Documents filed by date or project | Documents classified by strategic relevance and reuse potential |
| Knowledge locked in individual researchers | Knowledge captured in structured, searchable formats |
| Information retrieved on request | Information pushed to decision-makers at the right moment |
| Metrics: file count | Metrics: reuse rate, decision impact, time-to-insight |

## The ASIM Framework

### 1. Knowledge Classification

Not all information has equal strategic value. Apply a two-axis classification:

- **Uniqueness** — Is this information proprietary or publicly available?
- **Relevance** — How directly does it influence current strategic priorities?

| Quadrant | Action |
|---|---|
| High uniqueness + High relevance | Protect, deepen, and actively deploy |
| High uniqueness + Low relevance | Archive with periodic relevance reviews |
| Low uniqueness + High relevance | Monitor external sources; do not duplicate internally |
| Low uniqueness + Low relevance | Discard or minimal retention |

### 2. Information Currency Monitoring

Scientific information decays. A study published five years ago may be superseded. Assign each knowledge asset a **review date** based on how fast the field moves:

| Field Velocity | Review Cycle |
|---|---|
| Rapidly evolving (AI, genomics, novel materials) | 6–12 months |
| Moderately evolving (chemical engineering, energy) | 1–2 years |
| Stable (established mathematics, physical constants) | 3–5 years |

### 3. Knowledge Mapping

A knowledge map shows what the organisation knows, where the gaps are, and who holds critical expertise. It answers:
- Where are our knowledge strengths relative to our strategy?
- Which knowledge gaps are blocking progress?
- What knowledge exists only in individuals (key-person risk)?

### 4. Active Dissemination

Information that is not used is not an asset. Build active dissemination channels:
- **Weekly intelligence digests** — curated summaries of new internal and external findings relevant to current projects
- **Decision briefs** — 1-page summaries of what the evidence says on a specific strategic question
- **Cross-team knowledge sessions** — structured sharing of findings across departments

### 5. Measuring Information ROI

Track:
- **Time-to-insight**: How long does it take to answer a strategic research question?
- **Knowledge reuse rate**: What percentage of new projects draw on existing internal knowledge?
- **Decision reversal rate**: How often are decisions reversed due to information that was available but not surfaced in time?

## Organisational Enablers

- **Knowledge stewards** — Designated roles responsible for curating specific knowledge domains
- **Structured tagging** — Consistent taxonomy across all stored documents
- **Search infrastructure** — Full-text and semantic search across the knowledge base (this system provides both)
- **Retention policy** — Clear rules on what is kept, for how long, and why

## Summary

Active science information management is a competitive capability. Organisations that can turn their accumulated knowledge into faster, better decisions outperform those that simply store information and hope it gets found.
MD,
            ],

            // ── 3 ──────────────────────────────────────────────────────────────
            [
                'title'   => 'Trading Strategy Fundamentals: Five Core Approaches for Business Market Positioning',
                'summary' => 'An overview of five foundational trading and market positioning strategies — from cost leadership to niche specialisation — with criteria for choosing the right approach based on market conditions and organisational capability.',
                'tags'    => ['trading strategy', 'market positioning', 'business management', 'competitive strategy', 'Porter'],
                'content' => <<<MD
# Trading Strategy Fundamentals: Five Core Approaches for Business Market Positioning

A trading strategy defines how a business competes in its markets — what it offers, to whom, at what price, and on what terms. Without a deliberate positioning strategy, businesses compete on inertia rather than intent.

## Why Positioning Matters

Every market has structural forces that determine profitability: supplier power, buyer power, competitive rivalry, threat of new entrants, and threat of substitutes (Porter's Five Forces). Your trading strategy must position you to resist these forces, not be crushed by them.

## The Five Core Trading Strategies

### Strategy 1: Cost Leadership

**Win by offering the lowest total cost to the customer.**

- Requires relentless operational efficiency, economies of scale, and supply chain optimisation
- Defensible when: you have structural cost advantages (access to cheap inputs, proprietary processes, scale)
- Vulnerable when: a competitor achieves lower costs, or customers shift preference away from price

**Applicable to:** commodity markets, volume manufacturing, wholesale distribution

**Key metrics:** cost per unit, overhead ratio, inventory turns

---

### Strategy 2: Differentiation

**Win by offering something customers cannot get elsewhere and value enough to pay a premium for.**

Differentiation can be based on:
- Product features or performance
- Brand and reputation
- Service and reliability
- Innovation speed
- Proprietary technology or intellectual property

- Requires sustained investment in the differentiating capability
- Defensible when: differentiation is hard to replicate and customers perceive clear value
- Vulnerable when: differentiation erodes (competitors catch up) or premium buyers disappear

**Applicable to:** branded goods, high-technology products, professional services

**Key metrics:** price premium vs. market average, customer retention, Net Promoter Score

---

### Strategy 3: Niche / Focus

**Win by serving a specific segment better than generalists can.**

Niche strategy applies either cost focus (lowest cost within the niche) or differentiation focus (best product/service for the niche). The niche must be:
- Large enough to be profitable
- Small enough that large competitors ignore it
- Defensible (switching costs, relationships, specialised knowledge)

**Applicable to:** specialist manufacturers, B2B service providers, regional businesses

**Key metrics:** market share within niche, customer lifetime value, cost-to-serve ratio

---

### Strategy 4: Value Chain Integration

**Win by controlling more stages of the value chain than competitors.**

Integration can be:
- **Vertical upstream** (acquiring suppliers) — reduces input costs and improves supply security
- **Vertical downstream** (acquiring distributors or retailers) — improves margin capture and customer data
- **Horizontal** (acquiring competitors) — gains scale and market share

- Best suited to markets where margin is concentrated at a specific stage of the chain
- Requires capital and management capacity

**Key metrics:** gross margin by segment, supply chain resilience score, integration payback period

---

### Strategy 5: Platform and Ecosystem Strategy

**Win by creating a platform that others build on, generating network effects.**

A platform creates value by connecting two or more groups (buyers/sellers, developers/users, researchers/funders). Value grows with each participant added. Examples: marketplaces, data platforms, research networks.

- Defensible when: network effects create lock-in for all participants
- Requires: critical mass of participants before value is generated (the "cold start" problem)
- Vulnerable to: competing platforms poaching one side of the network

**Key metrics:** participant growth rate, transaction volume, cross-side network effect coefficient

---

## Choosing Your Strategy: A Decision Framework

| Condition | Recommended Strategy |
|---|---|
| You have structural cost advantages | Cost Leadership |
| Your product/service is genuinely unique and valued | Differentiation |
| You serve a narrow, defensible segment | Niche / Focus |
| Margin is concentrated at adjacent value chain stages | Vertical Integration |
| Your value grows with each additional user or partner | Platform / Ecosystem |

## The Stuck-in-the-Middle Trap

Companies that try to pursue cost leadership AND differentiation simultaneously without a clear primary strategy often end up "stuck in the middle" — too expensive to win on cost, too generic to win on premium. Choose a primary strategy, then optimise secondarily.

## Strategy Review Cadence

Trading strategies should be reviewed:
- Annually as part of the business planning cycle
- Immediately when a structural market change occurs (new entrant, regulatory shift, technology disruption)
- When key metrics show sustained deviation from target
MD,
            ],

            // ── 4 ──────────────────────────────────────────────────────────────
            [
                'title'   => 'Business Management in Research-Driven Organisations: Aligning Operations with Scientific Goals',
                'summary' => 'Practical frameworks for managing the business side of research organisations — covering resource allocation, project portfolio management, IP commercialisation, and balancing short-term operational demands with long-term scientific goals.',
                'tags'    => ['business management', 'research organisations', 'R&D management', 'IP strategy', 'portfolio management'],
                'content' => <<<MD
# Business Management in Research-Driven Organisations: Aligning Operations with Scientific Goals

Research organisations face a fundamental tension: science requires patience, iteration, and tolerance for failure, while business management demands predictable outputs, controlled costs, and measurable returns. Resolving this tension is the central challenge of managing a research-driven enterprise.

## The Dual Operating System

Effective research organisations run two systems simultaneously:

| System | Orientation | Key Metrics | Management Style |
|---|---|---|---|
| **Explore** | Long-horizon, uncertain | Discoveries, patents, publications | Autonomy, peer review, milestones |
| **Exploit** | Near-term, defined | Revenue, margin, delivery schedule | Accountability, KPIs, governance |

The failure mode of most research organisations is letting one system dominate. Pure Explore produces knowledge with no commercial path. Pure Exploit consumes existing assets without renewing them.

## Resource Allocation: The 70/20/10 Model

A proven allocation framework for research portfolios:

- **70%** — Core: optimising and extending what is already working
- **20%** — Adjacent: applying existing capabilities to new markets or products
- **10%** — Transformational: exploring fundamentally new capabilities or business models

This is not a rigid rule but a diagnostic tool. If your allocation deviates significantly, ask why and whether it reflects strategy or drift.

## Project Portfolio Management

Research portfolios must be managed as a whole, not as independent projects. Key principles:

### Stage-Gate Process
Divide projects into defined stages with explicit criteria for advancing, pausing, or terminating. Common gates:
1. Concept feasibility
2. Technical proof of concept
3. Commercial validation
4. Pilot / scale-up
5. Full deployment

At each gate, the project must demonstrate it meets the criteria for the next stage — not just that it is interesting scientifically.

### Portfolio Balance
Maintain balance across:
- **Time horizon** (short / medium / long)
- **Risk profile** (incremental / breakthrough)
- **Resource intensity** (small bets vs. concentrated investments)

### Kill Criteria
Define upfront what would cause a project to be terminated. Sunk cost bias keeps failed projects alive. A predetermined kill criterion removes the emotional component from the decision.

## Intellectual Property Strategy

Research organisations generate IP as a byproduct of their work. Managing it actively creates value.

| IP Type | Strategic Options |
|---|---|
| Patents | License, sell, use defensively, or publish to block competitors |
| Trade secrets | Protect through access controls and confidentiality agreements |
| Know-how | Embed in products/services; difficult to separate but highly valuable |
| Publications | Establish credibility; attract talent and partners |
| Data sets | License, sell, or use as a platform foundation |

**IP Audit** — Annually review your IP portfolio:
- Which assets are being actively used?
- Which have commercial potential that is not being realised?
- Which are costing money to maintain but producing no return?

## Financial Management Specifics for Research Organisations

**Grant and project accounting** — Research funding is often ring-fenced. Maintain clear separation between project budgets and operational budgets to ensure compliance and accurate project-level profitability.

**Overhead allocation** — Scientific staff and facilities are shared across projects. Establish a fair and consistent method for allocating overhead costs so project profitability figures are meaningful.

**Revenue diversification** — Research organisations dependent on a single funder or contract are fragile. Actively manage the funding mix: grants, contracts, licensing, product sales, consulting.

## Talent Management

Research talent is concentrated, scarce, and mobile. Key management priorities:
- **Retention**: Career development paths that do not force scientists into management to advance
- **Succession**: Document critical knowledge held by key individuals
- **Culture**: Protect the environment that attracted scientific talent in the first place

## Summary

Managing a research organisation well means running the science AND the business with equal rigour — using different tools for each, but always in service of the same long-term goals.
MD,
            ],

            // ── 5 ──────────────────────────────────────────────────────────────
            [
                'title'   => 'Research Commercialisation: Five Strategic Pathways from Discovery to Market',
                'summary' => 'A structured guide to the five main routes for commercialising research outputs — direct product development, licensing, spin-out ventures, strategic partnerships, and open innovation — with selection criteria for each pathway.',
                'tags'    => ['commercialisation', 'research strategy', 'technology transfer', 'spin-out', 'licensing', 'open innovation'],
                'content' => <<<MD
# Research Commercialisation: Five Strategic Pathways from Discovery to Market

Research that remains in the laboratory creates no commercial value. Commercialisation is the process of turning research outputs — inventions, data, know-how, processes — into products, services, or licensed technologies that generate revenue and societal impact.

There is no single correct commercialisation pathway. The right choice depends on the nature of the research, the organisation's capabilities, the competitive landscape, and the available resources.

## The Commercialisation Readiness Assessment

Before choosing a pathway, assess readiness across four dimensions:

| Dimension | Questions to Answer |
|---|---|
| **Technology readiness** | How close is the output to a deployable product or process? (TRL 1–9) |
| **Market readiness** | Is there a defined customer segment with a clear, validated need? |
| **Commercial capability** | Does the organisation have the resources and skills to execute the chosen pathway? |
| **IP position** | Is the output protectable? What freedom to operate exists? |

## Pathway 1: Direct Product or Service Development

**The organisation builds and sells the product or service itself.**

Best suited when:
- The research output is close to market-ready (TRL 7+)
- The organisation has or can acquire manufacturing, sales, and distribution capability
- The market is large enough to justify the investment
- The IP position is defensible

**Advantages:** Full control of IP, brand, and margin capture
**Disadvantages:** Requires the most capital, time, and operational capability; highest risk

**Key steps:** Product development, regulatory/certification compliance, go-to-market strategy, sales channel development

---

## Pathway 2: Licensing

**The organisation licenses its IP to third parties in exchange for royalties or fees.**

Best suited when:
- The organisation lacks the capability or appetite to commercialise directly
- Multiple market segments could use the technology (each requiring separate operational expertise)
- The IP is clearly defined and legally protectable

**Advantages:** Low operational overhead; generates revenue from existing IP; allows focus on research
**Disadvantages:** Licensee controls market execution; royalty rates are typically 3–8% of revenue; difficult to enforce in some jurisdictions

**Types of licensing:**
- **Exclusive** — One licensee in a defined territory or field; higher royalty rate; requires milestone obligations to prevent shelving
- **Non-exclusive** — Multiple licensees; lower rate per licensee but broader market coverage
- **Cross-licensing** — Exchange of IP rights with another organisation; used to resolve IP blockades or access complementary technology

---

## Pathway 3: Spin-Out / Spin-Off Venture

**A new, independent company is created to commercialise the research.**

Best suited when:
- The technology requires a dedicated team to develop and market
- The parent organisation is not structured for commercial operations
- External investment (venture capital, grants) is available to fund the venture
- The commercial opportunity is large enough to justify a standalone business

**Advantages:** Aligns incentives (equity ownership); attracts specialist commercial talent; isolates commercial risk from the parent
**Disadvantages:** Requires significant management attention; dilutes IP ownership; high failure rate for early-stage ventures

**Key success factors:**
- Strong founding team with both scientific and commercial expertise
- Clear IP assignment agreement between parent organisation and spin-out
- Defined investment plan and exit horizon
- Milestone-based governance

---

## Pathway 4: Strategic Partnerships and Joint Ventures

**The organisation partners with an established company to co-develop and bring the research to market.**

Best suited when:
- A commercial partner has the distribution, manufacturing, or regulatory capabilities the research organisation lacks
- Neither party can succeed alone but both benefit from combining assets
- The IP can be structured so that each party retains defined rights

**Types of partnerships:**
- **Research collaboration** — Shared R&D costs and IP
- **Development and supply agreement** — Research organisation develops; partner manufactures and sells
- **Joint venture** — New legal entity created with shared ownership

**Advantages:** Shared risk and investment; access to partner's market presence and capabilities
**Disadvantages:** IP complexity; alignment challenges; slower decision-making; partner dependency risk

---

## Pathway 5: Open Innovation and Knowledge Transfer

**The organisation releases research outputs openly, generating indirect commercial value.**

This is often underestimated as a commercialisation strategy. Open publication, open-source tools, and shared data sets can:
- Establish the organisation as the leading authority in a field
- Attract talent, partners, and customers
- Create a platform others build on (generating ecosystem value)
- Satisfy public funding obligations while building commercial reputation

**Best suited when:**
- The primary commercial model is services, consulting, or platform fees — not product sales
- The organisation benefits more from ecosystem growth than from IP exclusivity
- Regulatory or funding requirements mandate open access

**Advantages:** Low cost; builds reputation and network effects; removes IP maintenance burden
**Disadvantages:** Competitors can use the released knowledge; difficult to capture direct revenue from the output itself

---

## Selecting the Right Pathway: Decision Matrix

| Scenario | Recommended Pathway |
|---|---|
| High TRL, internal commercial capability, defensible IP | Direct development |
| High TRL, limited commercial capability, clear IP | Licensing |
| Breakthrough technology, large market, external funding available | Spin-out |
| Technology requires partner's market access or manufacturing | Strategic partnership |
| Reputation and ecosystem value outweigh direct IP value | Open innovation |

## Hybrid Approaches

Most successful commercialisation programmes use multiple pathways simultaneously:
- License non-core applications while developing core products directly
- Publish foundational research openly while patenting applied innovations
- Form a joint venture for one geography while licensing in others

The key is intentionality — choosing pathways based on strategic analysis, not default or inertia.
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
