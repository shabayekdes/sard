import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MessageSquare, MoreHorizontal, Edit, Trash2, Send } from 'lucide-react';
import { Task, TaskComment, User } from '@/types';

interface Props {
    task: Task;
    comments: TaskComment[];
    /** Optional; reserved for future use (e.g. mention UI). */
    currentUser?: User;
    onUpdate?: () => void;
}

export default function TaskComments({ task, comments, currentUser: _currentUser, onUpdate }: Props) {
    const { t } = useTranslation();
    const [newComment, setNewComment] = useState('');
    const [editingComment, setEditingComment] = useState<number | null>(null);
    const [editText, setEditText] = useState('');
    const [commentToDelete, setCommentToDelete] = useState<TaskComment | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!newComment.trim()) return;

        router.post(route('tasks.task-comments.store'), {
            task_id: task.id,
            comment_text: newComment.trim(),
            is_internal: false,
        }, {
            onSuccess: () => {
                setNewComment('');
                onUpdate?.();
            }
        });
    };

    const handleEdit = (comment: TaskComment) => {
        setEditingComment(comment.id);
        setEditText(comment.comment_text);
    };

    const handleUpdate = (commentId: number) => {
        const existing = comments.find((c) => c.id === commentId);
        const isInternal = existing?.is_internal === true || existing?.is_internal === 'true';
        router.put(route('tasks.task-comments.update', commentId), {
            task_id: task.id,
            comment_text: editText,
            is_internal: isInternal,
        }, {
            onSuccess: () => {
                setEditingComment(null);
                setEditText('');
                onUpdate?.();
            }
        });
    };

    const handleConfirmDeleteComment = () => {
        if (!commentToDelete) return;
        router.delete(route('tasks.task-comments.destroy', commentToDelete.id), {
            onSuccess: () => {
                setCommentToDelete(null);
                onUpdate?.();
            },
        });
    };

    return (
        <div className="space-y-4">
            {/* Comments List */}
            <div className="space-y-3">
                {comments.map((comment) => (
                    <div key={comment.id} className="flex space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div className="flex-1">
                            <div className="flex items-center justify-between mb-1">
                                <div className="flex items-center space-x-2">
                                    <span className="text-sm font-medium">{comment.user?.name}</span>
                                    <span className="text-xs text-gray-500">
                                        {new Date(comment.created_at).toLocaleString()}
                                    </span>
                                </div>
                                {(comment.can_update || comment.can_delete) && (
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="ghost" size="sm">
                                                <MoreHorizontal className="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" className="z-[9999]">
                                            {comment.can_update && (
                                                <DropdownMenuItem onClick={() => handleEdit(comment)}>
                                                    <Edit className="h-4 w-4 mr-2" />
                                                    {t('Edit')}
                                                </DropdownMenuItem>
                                            )}
                                            {comment.can_delete && (
                                                <DropdownMenuItem
                                                    onClick={() => setCommentToDelete(comment)}
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

                            {editingComment === comment.id ? (
                                <div className="space-y-2">
                                    <Textarea
                                        value={editText}
                                        onChange={(e) => setEditText(e.target.value)}
                                        rows={2}
                                    />
                                    <div className="flex space-x-2">
                                        <Button size="sm" onClick={() => handleUpdate(comment.id)}>
                                            {t('Save')}
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            onClick={() => setEditingComment(null)}
                                        >
                                            {t('Cancel')}
                                        </Button>
                                    </div>
                                </div>
                            ) : (
                                <p className="text-sm text-gray-700">{comment.comment_text}</p>
                            )}
                        </div>
                    </div>
                ))}

                {comments.length === 0 && (
                    <div className="text-center py-6 text-gray-500">
                        <MessageSquare className="h-8 w-8 mx-auto mb-2 text-gray-300" />
                        <p>{t('No comments yet. Be the first to comment!')}</p>
                    </div>
                )}
            </div>

            {/* Add Comment Form */}
            <form onSubmit={handleSubmit} className="space-y-3">
                <Textarea
                    value={newComment}
                    onChange={(e) => setNewComment(e.target.value)}
                    placeholder={t('Add a comment...')}
                    rows={3}
                />
                <div className="flex justify-end">
                    <Button type="submit" size="sm" disabled={!newComment.trim()}>
                        <Send className="h-4 w-4 mr-2" />
                        {t('Post Comment')}
                    </Button>
                </div>
            </form>

            <CrudDeleteModal
                isOpen={commentToDelete !== null}
                onClose={() => setCommentToDelete(null)}
                onConfirm={handleConfirmDeleteComment}
                itemName={commentToDelete?.comment_text ?? ''}
                entityName="Task comment"
            />
        </div>
    );
}