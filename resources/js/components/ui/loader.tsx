import * as React from "react"
import { TailSpin } from "react-loader-spinner"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@/lib/utils"

const loaderVariants = cva(
  "relative flex items-center justify-center",
  {
    variants: {
      variant: {
        default: "text-primary",
        secondary: "text-secondary",
        muted: "text-muted-foreground",
      },
      size: {
        sm: "w-8 h-8",
        default: "w-12 h-12",
        lg: "w-16 h-16",
        xl: "w-24 h-24",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
)

interface LoaderProps extends VariantProps<typeof loaderVariants> {
  className?: string
  fullScreen?: boolean
}

const Loader = React.forwardRef<HTMLDivElement, LoaderProps>(
  (
    {
      className,
      variant = "default",
      size = "default",
      fullScreen = false,
      ...props
    },
    ref
  ) => {
    const content = (
      <>
        <div className="absolute inset-0 flex items-center justify-center">
          <TailSpin
            visible={true}
            height="80"
            width="80"
            color="var(--primary, #205341)"
            ariaLabel="tail-spin-loading"
            radius="1"
            wrapperStyle={{}}
            wrapperClass=""
          />
        </div>
        <div className="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
          <img
            src="/images/loading.svg"
            alt=""
            className="w-2/3 h-2/3 max-w-[60%] max-h-[60%] object-contain"
          />
        </div>
      </>
    )

    const inner = (
      <div
        ref={ref}
        className={cn(
          loaderVariants({ variant, size }),
          !fullScreen && "animate-in fade-in duration-300",
          className
        )}
        role="status"
        aria-label="Loading"
        {...props}
      >
        {content}
      </div>
    )

    if (fullScreen) {
      return (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-background/60 backdrop-blur-sm animate-in fade-in duration-200"
          aria-hidden
        >
          {inner}
        </div>
      )
    }

    return inner
  }
)
Loader.displayName = "Loader"

export { Loader, loaderVariants }
