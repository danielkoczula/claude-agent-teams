---
name: agent-teams
description: Orchestrates multiple Claude Code sessions working together as a team with shared tasks, inter-agent messaging, and centralized management. Use when user asks to "create an agent team", "spawn teammates", "run tasks in parallel with multiple agents", "coordinate agents", or when a complex task benefits from parallel exploration (research, code review, debugging with competing hypotheses, cross-layer implementation). Requires CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS=1 to be enabled.
---

# Agent Teams

## Instructions

### Step 1: Check prerequisites

Before creating a team, verify:
- The `CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS` environment variable is set to `1`
- If not set, inform the user and provide the settings.json snippet:
  ```json
  {
    "env": {
      "CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS": "1"
    }
  }
  ```

### Step 2: Assess whether a team is appropriate

Use an agent team when the task has **independent, parallel workstreams**. Best use cases:
- Research and review (multiple teammates investigate different aspects simultaneously)
- New modules or features (each teammate owns a separate piece)
- Debugging with competing hypotheses (teammates test different theories in parallel)
- Cross-layer coordination (frontend, backend, tests owned by different teammates)

Do NOT use an agent team for:
- Sequential tasks where step B depends on step A
- Same-file edits (risk of overwrites)
- Simple, focused tasks — use subagents instead

### Step 3: Define the team structure

Before spawning, plan:
1. **Number of teammates**: start with 3–5; scale up only when genuinely needed. Aim for 5–6 tasks per teammate
2. **Roles**: assign each teammate a distinct, non-overlapping domain
3. **File ownership**: ensure no two teammates edit the same files
4. **Task size**: each task should produce a clear deliverable (a function, test file, or review)

### Step 4: Spawn the team

Tell Claude to create the team using natural language, specifying roles and task descriptions. Example prompt structure:

```
Create an agent team to [goal]. Spawn [N] teammates:
- One focused on [role 1]
- One focused on [role 2]
- One focused on [role 3]
Have them [coordination instructions].
```

The lead session creates the team, spawns teammates, and manages a shared task list. Teammates self-claim tasks when available.

### Step 5: Choose display mode

- **In-process** (default in any terminal): all teammates run inside main terminal. Use `Shift+Down` to cycle through teammates
- **Split panes** (requires tmux or iTerm2): each teammate gets its own pane. Set in settings.json:
  ```json
  { "teammateMode": "in-process" }
  ```
  Or override per-session: `claude --teammate-mode in-process`

### Step 6: Coordinate the team

- **Talk to teammates directly**: in-process — use `Shift+Down` to cycle, then type; split pane — click into pane
- **Assign tasks explicitly**: tell the lead which task goes to which teammate, or let teammates self-claim
- **Require plan approval** for risky tasks:
  ```
  Spawn a [role] teammate to [task]. Require plan approval before they make any changes.
  ```
- **Monitor progress**: check in regularly. Redirect approaches that aren't working
- **Wait for teammates**: if the lead starts implementing instead of delegating, say:
  ```
  Wait for your teammates to complete their tasks before proceeding
  ```

### Step 7: Shut down and clean up

Shut down individual teammates first:
```
Ask the [role] teammate to shut down
```

Then clean up team resources via the lead:
```
Clean up the team
```

Always use the lead to run cleanup. Never run cleanup from a teammate.

## Examples

### Example 1: Parallel code review

User says: "Review PR #142 focusing on security, performance, and test coverage"

Actions:
1. Create team with 3 reviewers — security, performance, test coverage
2. Each reviewer works from the same PR with a different lens
3. Lead synthesizes findings after all three finish

Result: Comprehensive review with no domain overlap

### Example 2: Debug with competing hypotheses

User says: "Users report the app exits after one message. Investigate."

Actions:
1. Spawn 5 teammates, each with a different root-cause hypothesis
2. Have teammates message each other to challenge each other's theories
3. Update a shared findings doc with the consensus

Result: The surviving theory is the actual root cause

### Example 3: Cross-layer feature implementation

User says: "Implement the new user authentication flow across frontend, backend, and tests"

Actions:
1. Spawn 3 teammates: frontend, backend, tests
2. Ensure each owns distinct files (no overlap)
3. Lead coordinates dependencies between layers

Result: Parallel implementation without file conflicts

## Common Issues

### Error: Teammates not appearing after team creation
Cause: Task may not be complex enough, or tmux not installed for split-pane mode
Solution:
- Press `Shift+Down` to cycle through in-process teammates
- Verify tmux: `which tmux`
- Explicitly request a team and describe the task complexity

### Error: Too many permission prompts
Cause: Teammate permission requests bubble up to the lead
Solution: Pre-approve common operations in permission settings before spawning teammates

### Error: Teammate stopped on an error
Cause: Teammate encountered an unrecoverable error
Solution:
- Navigate to the teammate with `Shift+Down`
- Give additional instructions directly
- Or spawn a replacement teammate to continue the work

### Error: Lead shuts down before work is done
Cause: Lead incorrectly assessed completion
Solution: Tell the lead to keep going and wait for teammates to finish

### Error: Task status appears stuck / blocked tasks not unblocking
Cause: Teammate failed to mark a task as completed
Solution: Check if the work is actually done; update task status manually or ask the lead to nudge the teammate

### Error: Orphaned tmux sessions after team ends
Cause: Incomplete cleanup
Solution:
```bash
tmux ls
tmux kill-session -t [session-name]
```

## Known Limitations

- No session resumption with in-process teammates (`/resume` and `/rewind` don't restore teammates)
- Task status can lag — teammates sometimes fail to mark tasks complete
- One team per session — clean up before starting a new one
- No nested teams — only the lead can spawn teammates
- Permissions set at spawn time — all teammates start with the lead's permission mode
- Split panes not supported in VS Code integrated terminal, Windows Terminal, or Ghostty
