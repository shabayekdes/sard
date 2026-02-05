import { useState, useEffect } from 'react'
import { Head, Link, router, usePage } from '@inertiajs/react'
import { PageTemplate } from '@/components/page-template'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Badge } from '@/components/ui/badge'
import { RichTextField } from '@/components/ui/rich-text-field'
import { Save } from 'lucide-react'
import { useTranslation } from 'react-i18next'
import { toast } from '@/components/custom-toast'

interface EmailTemplate {
  id: number
  name: string | Record<string, string>
  from: string | Record<string, string>
  type: string
  subject: Record<string, string>
  content: Record<string, string>
}

interface Language {
  code: string
  name: string
  countryCode: string
}

interface Props {
  template: EmailTemplate
  templateTypes: string[]
  languages: Language[]
  variables: Record<string, string>
}

export default function EmailTemplateShow({ template, templateTypes, languages, variables }: Props) {
  const { t, i18n } = useTranslation()
  const { flash } = usePage().props as any
  const allowedLanguages = languages.filter((language) => ['en', 'ar'].includes(language.code))
  const [templateType, setTemplateType] = useState(template.type || '')
  const [templateNames, setTemplateNames] = useState<Record<string, string>>(() => {
    if (typeof template.name === 'string') {
      return { en: template.name, ar: template.name }
    }
    return { en: template.name?.en || '', ar: template.name?.ar || '' }
  })
  const [fromNames, setFromNames] = useState<Record<string, string>>(() => {
    if (typeof template.from === 'string') {
      return { en: template.from, ar: template.from }
    }
    return { en: template.from?.en || '', ar: template.from?.ar || '' }
  })
  const [currentLang, setCurrentLang] = useState(allowedLanguages[0]?.code || 'en')
  const currentLocale = i18n.language || 'en'
  const templateName =
    typeof template.name === 'string'
      ? template.name
      : template.name[currentLocale] || template.name.en || Object.values(template.name)[0] || ''
  const [templateLangs, setTemplateLangs] = useState(() => {
    const data: Record<string, { subject: string; content: string }> = {}
    allowedLanguages.forEach((language) => {
      data[language.code] = {
        subject: template.subject?.[language.code] || '',
        content: template.content?.[language.code] || ''
      }
    })
    return data
  })

  const handleSubjectChange = (lang: string, subject: string) => {
    setTemplateLangs(prev => ({
      ...prev,
      [lang]: { ...prev[lang], subject }
    }))
  }

  const handleContentChange = (lang: string, content: string) => {
    setTemplateLangs(prev => ({
      ...prev,
      [lang]: { ...prev[lang], content }
    }))
  }

  const handleSave = () => {
    alert('Save functionality will be implemented later')
  }

  // Handle flash messages
  useEffect(() => {
    if (flash?.success) {
      toast.success(flash.success)
    }
    if (flash?.error) {
      toast.error(flash.error)
    }
  }, [flash])

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Email Templates'), href: route('email-templates.index') },
    { title: templateName }
  ]

  const pageActions: any[] = []
  const formatTemplateType = (type: string) =>
    type
      .toLowerCase()
      .split('_')
      .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
      .join(' ')

  return (
    <PageTemplate
      title={templateName}
      url={route('email-templates.show', template.id)}
      breadcrumbs={breadcrumbs}
      actions={pageActions}
    >
      <Head title={`Edit Template - ${templateName}`} />

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2 space-y-6">
          <Card>
            <CardHeader>
              <div className="flex justify-between items-center">
                <CardTitle>{t("Template Settings")}</CardTitle>
                <Button
                  onClick={() => {
                    router.put(route('email-templates.update-settings', template.id), {
                      name: {
                        en: templateNames.en,
                        ar: templateNames.ar
                      },
                      from: {
                        en: fromNames.en,
                        ar: fromNames.ar
                      },
                      type: templateType
                    })
                  }}
                  size="sm"
                >
                  <Save className="h-4 w-4 mr-2" />
                  {t("Save Changes")}
                </Button>
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-2">
                <Label>{t("Template Type")}</Label>
                <Select value={templateType} onValueChange={setTemplateType}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder={t("Select template type")} />
                  </SelectTrigger>
                  <SelectContent>
                    {templateTypes.map((type) => (
                      <SelectItem key={type} value={type}>
                        {formatTemplateType(type)}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-3">
                <Label>{t("Template Name")}</Label>
                <div className="grid gap-3 sm:grid-cols-2">
                  {allowedLanguages.map((language) => (
                    <div key={`name-${language.code}`} className="grid gap-2">
                      <Label htmlFor={`name-${language.code}`} className="text-xs text-muted-foreground">
                        {language.name} ({language.code.toUpperCase()})
                      </Label>
                      <Input
                        id={`name-${language.code}`}
                        value={templateNames[language.code] || ''}
                        onChange={(e) =>
                          setTemplateNames((prev) => ({ ...prev, [language.code]: e.target.value }))
                        }
                        placeholder={t("Enter template name")}
                        className="focus:ring-2 focus:ring-primary"
                      />
                    </div>
                  ))}
                </div>
              </div>

              <div className="space-y-3">
                <Label>{t("From Name")}</Label>
                <div className="grid gap-3 sm:grid-cols-2">
                  {allowedLanguages.map((language) => (
                    <div key={`from-${language.code}`} className="grid gap-2">
                      <Label htmlFor={`from-${language.code}`} className="text-xs text-muted-foreground">
                        {language.name} ({language.code.toUpperCase()})
                      </Label>
                      <Input
                        id={`from-${language.code}`}
                        value={fromNames[language.code] || ''}
                        onChange={(e) =>
                          setFromNames((prev) => ({ ...prev, [language.code]: e.target.value }))
                        }
                        placeholder={t("Enter from name (e.g., {app_name}, Support Team)")}
                        className="focus:ring-2 focus:ring-primary"
                      />
                    </div>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <div className="flex justify-between items-center">
                <div>
                  <CardTitle>{t("Email Content")}</CardTitle>
                  <p className="text-sm text-muted-foreground mt-1">
                    {t("Customize email content for different languages")}
                  </p>
                </div>
                <Button
                  onClick={() => {
                    const currentContent = templateLangs[currentLang]
                    if (currentContent) {
                      router.put(route('email-templates.update-content', template.id), {
                        lang: currentLang,
                        subject: currentContent.subject,
                        content: currentContent.content
                      })
                    }
                  }}
                  size="sm"
                  className="shrink-0"
                >
                  <Save className="h-4 w-4 mr-2" />
                  {t("Save Content")}
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              <Tabs defaultValue={allowedLanguages[0]?.code} onValueChange={setCurrentLang} className="w-full">
                <div className="mb-4">
                  <div className="overflow-x-auto">
                    <TabsList className="inline-flex h-auto p-1 w-max">
                      {allowedLanguages.map((language) => (
                        <TabsTrigger
                          key={language.code}
                          value={language.code}
                          className="text-xs px-3 py-2 whitespace-nowrap data-[state=active]:bg-primary data-[state=active]:text-primary-foreground"
                        >
                          {language.code.toUpperCase()}
                        </TabsTrigger>
                      ))}
                    </TabsList>
                  </div>
                </div>

                {allowedLanguages.map((language) => (
                  <TabsContent key={language.code} value={language.code} className="space-y-6 mt-6">
                    <div className="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                      <Badge variant="default" className="px-3 py-1">
                        {language.code.toUpperCase()}
                      </Badge>
                      <div>
                        <span className="font-medium">{language.name}</span>
                        <p className="text-xs text-muted-foreground">{t("Edit email content for this language")}</p>
                      </div>
                    </div>

                    <div className="space-y-4">
                      <div className="grid gap-3">
                        <Label htmlFor={`subject-${language.code}`} className="text-sm font-medium">
                          {t("Email Subject")}
                        </Label>
                        <Input
                          id={`subject-${language.code}`}
                          value={templateLangs[language.code]?.subject || ''}
                          onChange={(e) => handleSubjectChange(language.code, e.target.value)}
                          placeholder={t("Enter email subject (you can use variables like {app_name})")}
                          className="focus:ring-2 focus:ring-primary"
                        />
                      </div>

                      <div className="space-y-3">
                        <RichTextField
                          label="Email Content"
                          value={templateLangs[language.code]?.content || ''}
                          onChange={(content) => handleContentChange(language.code, content)}
                          placeholder={t("Write your email content here. You can use HTML formatting and variables...")}
                          className="min-h-[300px]"
                        />
                        <p className="text-xs text-muted-foreground">
                          💡 {t("Tip: Use the variables from the sidebar to personalize your emails")}
                        </p>
                      </div>
                    </div>
                  </TabsContent>
                ))}
              </Tabs>
            </CardContent>
          </Card>
        </div>

        <div>
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <span>{t("Available Variables")}</span>
                <Badge variant="secondary" className="text-xs">
                  {Object.keys(variables).length}
                </Badge>
              </CardTitle>
              <p className="text-sm text-muted-foreground mt-1">
                {t("Click to copy variables to use in your email content")}
              </p>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {Object.entries(variables).map(([variable, description]) => (
                  <div
                    key={variable}
                    className="group p-3 bg-muted/50 rounded-lg border hover:bg-muted/80 cursor-pointer transition-colors"
                    onClick={async () => {
                      try {
                        await navigator.clipboard.writeText(variable)
                        toast.success(`Copied ${variable} to clipboard`)
                      } catch (err) {
                        // Fallback for older browsers
                        const textArea = document.createElement('textarea')
                        textArea.value = variable
                        document.body.appendChild(textArea)
                        textArea.select()
                        document.execCommand('copy')
                        document.body.removeChild(textArea)
                        toast.success(`Copied ${variable} to clipboard`)
                      }
                    }}
                  >
                    <div className="flex items-center justify-between">
                      <code className="text-sm font-mono text-primary font-medium bg-background px-1.5 py-0.5 rounded">
                        {variable}
                      </code>
                      <div className="opacity-0 group-hover:opacity-100 transition-opacity">
                        <Badge variant="outline" className="text-xs">
                          {t("Click to copy")}
                        </Badge>
                      </div>
                    </div>
                    <p className="text-xs text-muted-foreground mt-2 leading-relaxed">
                      {description}
                    </p>
                  </div>
                ))}
              </div>
              <div className="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p className="text-xs text-blue-700">
                  💡 <strong>{t("Tip")}:</strong> {t("These variables will be automatically replaced with actual values when emails are sent.")}
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </PageTemplate>
  )
}