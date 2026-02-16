import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('requiredSkillsManager', (initialSkills = []) => {
    const resolved = Array.isArray(initialSkills) && initialSkills.length > 0
        ? initialSkills.map(s => ({
            skill_name: s.skill_name ?? '',
            weight: s.weight ?? 1,
            min_proficiency: s.min_proficiency ?? '',
        }))
        : [{ skill_name: '', weight: 1, min_proficiency: '' }];
    return {
        skills: resolved,
        addSkill() {
            this.skills.push({ skill_name: '', weight: 1, min_proficiency: '' });
        },
        removeSkill(index) {
            this.skills.splice(index, 1);
            if (this.skills.length === 0) {
                this.skills.push({ skill_name: '', weight: 1, min_proficiency: '' });
            }
        },
    };
});

Alpine.start();
