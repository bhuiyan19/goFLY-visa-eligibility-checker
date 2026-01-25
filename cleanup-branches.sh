#!/bin/bash

# Branch Cleanup Script
# This script deletes unnecessary local branches

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║              Branch Cleanup - goFLY Visa Checker             ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Make sure we're on claude branch
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "claude/visa-eligibility-checker-oZjzM" ]; then
    echo "⚠️  Switching to claude/visa-eligibility-checker-oZjzM branch..."
    git checkout claude/visa-eligibility-checker-oZjzM
    echo ""
fi

echo "Current branch: $(git branch --show-current) ✅"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# List branches to delete
BRANCHES_TO_DELETE=(
    "gh-pages"
    "add-wordpress-integration-to-main"
    "feat/wordpress-integration-pr"
    "wordpress-integration-final"
)

echo "🗑️  Branches to be deleted:"
echo ""
for branch in "${BRANCHES_TO_DELETE[@]}"; do
    if git show-ref --verify --quiet "refs/heads/$branch"; then
        echo "   ✓ $branch (exists)"
    else
        echo "   - $branch (not found)"
    fi
done
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Ask for confirmation
read -p "Do you want to delete these branches? (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Cleanup cancelled."
    exit 0
fi

echo ""
echo "🗑️  Deleting branches..."
echo ""

# Delete each branch
DELETED_COUNT=0
for branch in "${BRANCHES_TO_DELETE[@]}"; do
    if git show-ref --verify --quiet "refs/heads/$branch"; then
        git branch -D "$branch" 2>/dev/null
        if [ $? -eq 0 ]; then
            echo "   ✅ Deleted: $branch"
            DELETED_COUNT=$((DELETED_COUNT + 1))
        else
            echo "   ❌ Failed to delete: $branch"
        fi
    fi
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Cleanup complete! Deleted $DELETED_COUNT branches."
echo ""
echo "📊 Remaining branches:"
git branch
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🏆 Production branch: claude/visa-eligibility-checker-oZjzM"
echo "⚠️  Keep but don't use: main"
echo ""
echo "✅ Your repository is now clean and organized!"
echo ""
