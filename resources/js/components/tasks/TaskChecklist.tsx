import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { CheckSquare, Square, Plus, MoreHorizontal, Edit, Trash2, Calendar, User } from 'lucide-react';
import { Task, TaskChecklist as ChecklistItem } from '@/types';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';

interface Props {
    task: Task;
    checklist: ChecklistItem[];
    onUpdate?: () => void;
}

export default function TaskChecklist({ task, checklist, onUpdate }: Props) {
    const { t } = useTranslation();
    const [newItem, setNewItem] = useState('');
    const [editingItem, setEditingItem] = useState<number | null>(null);
    const [editData, setEditData] = useState({
        title: '',
        assigned_to: '',
        due_date: ''
    });
    const [checklistItemToDelete, setChecklistItemToDelete] = useState<ChecklistItem | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!newItem.trim()) return;

        router.post(route('task-checklists.store', task.id), {
            title: newItem,
            assigned_to: '',
            due_date: ''
        }, {
            onSuccess: () => {
                setNewItem('');
                onUpdate?.();
            }
        });
    };

    const handleToggle = (itemId: number) => {
        router.post(route('task-checklists.toggle', itemId), {}, {
            onSuccess: () => {
                onUpdate?.();
            }
        });
    };

    const handleEdit = (item: ChecklistItem) => {
        setEditingItem(item.id);
        setEditData({
            title: item.title,
            assigned_to: item.assigned_to?.id?.toString() || '',
            due_date: item.due_date || ''
        });
    };

    const handleUpdate = (itemId: number) => {
        router.put(route('task-checklists.update', itemId), editData, {
            onSuccess: () => {
                setEditingItem(null);
                setEditData({ title: '', assigned_to: '', due_date: '' });
                onUpdate?.();
            }
        });
    };

    const handleConfirmDeleteChecklistItem = () => {
        if (!checklistItemToDelete) return;
        router.delete(route('task-checklists.destroy', checklistItemToDelete.id), {
            onSuccess: () => {
                setChecklistItemToDelete(null);
                onUpdate?.();
            },
        });
    };

    const completedCount = checklist.filter(item => item.is_completed).length;
    const progressPercentage = checklist.length > 0 ? (completedCount / checklist.length) * 100 : 0;

    return (
        <div className="space-y-4">
            {/* Progress Bar */}
            {checklist.length > 0 && (
                <div className="space-y-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-600">{t('Progress')}</span>
                        <span className="text-gray-600">
                            {t('{{completed}}/{{total}} completed', {
                                completed: completedCount,
                                total: checklist.length
                            })}
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            className="bg-green-600 h-2 rounded-full transition-all" 
                            style={{ width: `${progressPercentage}%` }}
                        />
                    </div>
                </div>
            )}

            {/* Checklist Items */}
            <div className="space-y-2">
                {checklist.map((item) => (
                    <div key={item.id} className="flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                        <button
                            onClick={() => handleToggle(item.id)}
                            className="mt-0.5 text-gray-400 hover:text-gray-600"
                        >
                            {item.is_completed ? (
                                <CheckSquare className="h-5 w-5 text-green-600" />
                            ) : (
                                <Square className="h-5 w-5" />
                            )}
                        </button>

                        <div className="flex-1 min-w-0">
                            {editingItem === item.id ? (
                                <div className="space-y-3">
                                    <Input
                                        value={editData.title}
                                        onChange={(e) => setEditData({...editData, title: e.target.value})}
                                        placeholder={t('Checklist item title')}
                                    />
                                    <div className="flex space-x-2">
                                        <Button size="sm" onClick={() => handleUpdate(item.id)}>
                                            {t('Save')}
                                        </Button>
                                        <Button 
                                            size="sm" 
                                            variant="outline" 
                                            onClick={() => setEditingItem(null)}
                                        >
                                            {t('Cancel')}
                                        </Button>
                                    </div>
                                </div>
                            ) : (
                                <>
                                    <div className={`text-sm ${item.is_completed ? 'line-through text-gray-500' : 'text-gray-900'}`}>
                                        {item.title}
                                    </div>
                                    {(item.assigned_to || item.due_date) && (
                                        <div className="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                            {item.assigned_to && (
                                                <div className="flex items-center space-x-1">
                                                    <User className="h-3 w-3" />
                                                    <span>{item.assigned_to.name}</span>
                                                </div>
                                            )}
                                            {item.due_date && (
                                                <div className="flex items-center space-x-1">
                                                    <Calendar className="h-3 w-3" />
                                                    <span>{new Date(item.due_date).toLocaleDateString()}</span>
                                                </div>
                                            )}
                                        </div>
                                    )}
                                </>
                            )}
                        </div>

                        {(item.can_update || item.can_delete) && (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" size="sm">
                                        <MoreHorizontal className="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="z-[9999]">
                                    {item.can_update && (
                                        <DropdownMenuItem onClick={() => handleEdit(item)}>
                                            <Edit className="h-4 w-4 mr-2" />
                                            {t('Edit')}
                                        </DropdownMenuItem>
                                    )}
                                    {item.can_delete && (
                                        <DropdownMenuItem 
                                            onClick={() => setChecklistItemToDelete(item)}
                                            className="text-red-600"
                                        >
                                            <Trash2 className="h-4 w-4 mr-2" />
                                            {t('Delete')}
                                        </DropdownMenuItem>
                                    )}
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>
                ))}

                {checklist.length === 0 && (
                    <div className="text-center py-6 text-gray-500">
                        <CheckSquare className="h-8 w-8 mx-auto mb-2 text-gray-300" />
                        <p>{t('No checklist items yet. Add your first item!')}</p>
                    </div>
                )}
            </div>

            {/* Add New Item */}
            <form onSubmit={handleSubmit} className="flex space-x-2">
                <Input
                    value={newItem}
                    onChange={(e) => setNewItem(e.target.value)}
                    placeholder={t('Add checklist item...')}
                    className="flex-1"
                />
                <Button type="submit" size="sm" disabled={!newItem.trim()}>
                    <Plus className="h-4 w-4 mr-2" />
                    {t('Add')}
                </Button>
            </form>

            <CrudDeleteModal
                isOpen={checklistItemToDelete !== null}
                onClose={() => setChecklistItemToDelete(null)}
                onConfirm={handleConfirmDeleteChecklistItem}
                itemName={checklistItemToDelete?.title ?? ''}
                entityName="Checklist item"
            />
        </div>
    );
}